<?php
namespace App\Controller;

use App\Form\PasswordNewType;
use App\Form\PasswordResetType;
use App\Form\UserImageType;
use App\Form\UserUpdateType;
use App\Service\FacebookApiException;
use App\Service\GoogleAuthenticator;
use App\Utils\MailManagerUtils;
use App\Utils\RecaptchaUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Entity\Config;
use App\Form\UserType;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserController extends Controller
{
    /**
     * @var Session
     */
    private $session;

    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer, RecaptchaUtils $recaptchaUtils, EntityManagerInterface $emi, Facebook $fb, GoogleAuthenticator $google)
    {
        $fbLink = $fb->fbLogin();
        $googleLink = $google->getGoogleLoginLink();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $captcha = $recaptchaUtils->check($request->get('g-recaptcha-response'),$request->getClientIp());
            if($captcha == false){
                $this->session->getFlashBag()->add('danger', 'Wypełnij formularz ponownie. Błąd captchy');

                return $this->render('front/register.html.twig',array(
                    'form' => $form->createView()
                ));
            }
            $user->setRoles(array('ROLE_USER'));
            $user->setIsActive(0);
            $user->setIsEnabledByAdmin(0);
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setHash(uniqid("", true));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->session = new Session();
            $this->session->getFlashBag()->add('success', 'Zostałeś zarejestrowany. Potwierdź rejestrację klikając w link w przesłanej wiadomości email');

            $config = $this->getDoctrine()
                ->getRepository(Config::class)
                ->find(1);

            $message = (new Swift_Message('Formularz rejestracyjny z '.$config->getTitle()))
                ->setFrom($config->getEmail())
                ->setReplyTo($config->getEmail())
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/register_form.html.twig',
                        [
                            'user' => $user,
                            'config' => $config,
                            'fbLink' => $fbLink,
                            'googleLink' => $googleLink
                        ]
                    ),
                    'text/html'
                )
            ;
            $mailManager = new MailManagerUtils($emi);
            $mailer->send($message);
            $mailBody = $this->renderView('emails/user_new_created.html.twig');
            $mailManager->sendEmail($mailBody,['subject' => 'Nowy użytkownik się zarejestrował - '.$config->getTitle()],$config->getEmail(),$mailer);
            $mailManager->sendEmail($mailBody,['subject' => 'Nowy użytkownik się zarejestrował - '.$config->getTitle()],'pawel@grupaformat.pl',$mailer);

            return $this->redirectToRoute('login');
        }

        return $this->render('front/register.html.twig',array(
            'user' => $user,
            'form' => $form->createView(),
            'fbLink' => $fbLink,
            'googleLink' => $googleLink
        ));
    }

    /**
     * @param string $h
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function enable(string $h)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['hash' => $h]);
        if($user){
            $user->setIsActive(1);
            $user->setHash(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->session->getFlashBag()->add('success', 'Użytkownik został aktywowany. Możesz się zalogować');
        }

        return $this->redirectToRoute('login');
    }


    /**
     * @return Response
     */
    public function account(Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($this->getUser());
        $form = $this->createForm(UserImageType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userImage = $form->get('user_image')->getData();

            if ($userImage) {
                $originalFilename = pathinfo($userImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $userImage->guessExtension();
                try {
                    $userImage->move(
                        $this->getParameter('user_image_directory'),
                        $newFilename
                    );
                    $this->session->getFlashBag()->add('success', 'Zdjęcie zostało poprawnie dodane');
                } catch (FileException $e) {
                    $this->session->getFlashBag()->add('danger', 'Zdjęcie nie zostało poprawnie dodane');
                }

                $user->setUserImage($this->getParameter('site_url').$this->getParameter('user_image_directory').'/'.$newFilename);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
        }

        return $this->render('front/account.html.twig',array(
            'user' => $user,
            'form' => $form->createView()
        ));
    }

    /**
     * @return Response
     */
    public function deleteImage()
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($this->getUser());
        $fi = explode('/',$user->getUserImage());
        $fileName = end($fi);
        $filesystem = new Filesystem();
        if($filesystem->exists($this->getParameter('user_image_directory').'/'.$fileName)){
            $filesystem->remove($this->getParameter('user_image_directory').'/'.$fileName);
        }
        $user->setUserImage('');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $this->session->getFlashBag()->add('success', 'Zdjęcie profilowe zostało usunięte.');

        return $this->redirectToRoute('front_user_account');
    }

    /**
     * @return Response
     */
    public function resetPassword(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(PasswordResetType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            try{
                $userFound = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findOneBy((['email' => $user['email']]));
            }catch(UsernameNotFoundException $exception)
            {
                $this->session->getFlashBag()->add('success', 'Link do zmiany hasła został wysłany');
            }
            if($userFound) {
                $userFound->setHash(uniqid("", true));

                $em = $this->getDoctrine()->getManager();
                $em->persist($userFound);
                $em->flush();

                $config = $this->getDoctrine()
                    ->getRepository(Config::class)
                    ->find(1);

                $message = (new Swift_Message('Formularz zmiany hasła z '.$config->getTitle()))
                    ->setFrom($config->getEmail(),$config->getTitle())
                    ->setReplyTo($config->getEmail())
                    ->setTo($userFound->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/account_password_reset.html.twig',
                            ['user' => $userFound, 'config' => $config]
                        ),
                        'text/html'
                    )
                ;
                $mailer->send($message);
                $this->session->getFlashBag()->add('success', 'Link do zmiany hasła został wysłany');
            } else {
                $this->session->getFlashBag()->add('danger', 'Podany użytkownik nie został znaleziony');
            }
        }
        return $this->render('front/password_reset.html.twig',array(
            'form'=> $form->createView()
        ));
    }

    /**
     * @param string $h
     */
    public function newPassword(string $h, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['hash' => $h]);
        if($user){
            $user->setHash($h);
            $form = $this->createForm(PasswordNewType::class, $user);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $user->setHash(NULL);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->session->getFlashBag()->add('success', 'Hasło zostało zmienione. Zapraszamy do logowania.');

                return $this->redirectToRoute('login');
            }

            return $this->render('front/password_new.html.twig',array(
                'form'=> $form->createView(),
                'user' => $user
            ));
        }

        return $this->redirectToRoute('login');
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($this->getUser());
        if($user){
            $form = $this->createForm(UserUpdateType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->session->getFlashBag()->add('success', 'Użytkownik został zmieniony');

                return $this->render('front/account_user_edit.html.twig',array(
                    'user'=> $user,
                    'form'=> $form->createView()
                ));
            }

            return $this->render('front/account_user_edit.html.twig',array(
                'user'=> $user,
                'form'=> $form->createView()
            ));
        }else {
            $this->session->getFlashBag()->add('danger', 'Użytkownik nie został znaleziony');

            return $this->redirectToRoute('front_user_account');
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function updateUserData($recruitmentId, Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($this->getUser());
        if($user){
            $form = $this->createForm(UserUpdateType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->session->getFlashBag()->add('success', 'Dane użytkownika zostały uzupełnione');

                return $this->redirectToRoute('front_user_recruitment_user_new',['recruitmentId' => $recruitmentId]);
            }

            return $this->render('front/account_user_update.html.twig',array(
                'user'=> $user,
                'form'=> $form->createView()
            ));
        }else {
            $this->session->getFlashBag()->add('danger', 'Użytkownik nie został znaleziony');

            return $this->redirectToRoute('front_user_account');
        }
    }

    public function fbLogin()
    {
        if (!session_id()) {
            session_start();
        }
        $fb = new \Facebook\Facebook([
            'app_id' => '2678733842225422',
            'app_secret' => '5985eed7f1dd803d22d75a12ce3b3393',
            'default_graph_version' => 'v2.2',
            'persistent_data_handler'=>'session'
        ]);

        $helper = $fb->getRedirectLoginHelper();
        if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }
        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl($this->getParameter('site_url').'fb_callback', $permissions);

        $loginLink = htmlspecialchars($loginUrl);

        return $loginLink;
    }

    public function fbCallback()
    {
        if (!session_id()) {
            session_start();
        }
        $fb = new \Facebook\Facebook([
            'app_id' => '2678733842225422', // Replace {app-id} with your app id
            'app_secret' => '5985eed7f1dd803d22d75a12ce3b3393',
            'default_graph_version' => 'v2.2',
            'persistent_data_handler'=>'session'
        ]);

        $helper = $fb->getRedirectLoginHelper();
        if (isset($_GET['state'])) {
            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
        }
        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        echo '<h3>Access Token</h3>';
        $oAuth2Client = $fb->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        echo '<h3>Metadata</h3>';
        $tokenMetadata->validateAppId('2678733842225422');
        $tokenMetadata->validateExpiration();

        if (! $accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
        }

        $_SESSION['fb_access_token'] = (string) $accessToken;

        return $this->redirectToRoute('fb_register');
    }

    public function fbRegister(Request $request, \Swift_Mailer $mailer, EntityManagerInterface $emi)
    {
        if (!session_id()) {
            session_start();
        }
        try {
            $fb = new \Facebook\Facebook([
                'app_id' => '2678733842225422',
                'app_secret' => '5985eed7f1dd803d22d75a12ce3b3393',
                'default_graph_version' => 'v2.2',
                'default_access_token' => $_SESSION['fb_access_token'], // optional,
                'persistent_data_handler'=>'session'
            ]);

            $response = $fb->get('/me?fields=name,email,first_name,last_name,id', $_SESSION['fb_access_token']);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $me = $response->getGraphUser();
        echo 'Logged in as ' . $me->getName();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['email'=>$me->getEmail()],null,1);

        if(count($user) > 0){

            $user2 = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($user[0]->getId());
            if($user2->getIsActive()){
                $token = new UsernamePasswordToken($user2, null, 'main', $user2->getRoles());
                $this->get('security.token_storage')->setToken($token);

                $this->get('session')->set('_security_main', serialize($token));

                // Fire the login event manually
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                if($user2->getFacebookUserId()){
                }else{
                    $user2->setFacebookUserId($me->getId());
                    if(empty($user2)){
                        $user2->setUserImage('//graph.facebook.com/'.$me->getId().'/picture?width=150&amp;height=150');
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user2);
                    $em->flush();
                }

            }else{
                $this->session->getFlashBag()->add('danger', 'Twoje konto jest wyłączone');

                return $this->redirectToRoute('index');
            }
        }else{
            $us = new User();
            $us->setEmail($me->getEmail());
            $us->setFirstName($me->getFirstName());
            $us->setLastName($me->getLastName());
            $us->setRegulations(1);
            $us->setRegulationFromRegister(1);
            $us->setMarketingRegulations(0);
            $us->setRoles(["ROLE_USER"]);
            $us->setIsActive(1);
            $us->setIsEnabledByAdmin(1);
            $us->setFacebookUserId($me->getId());
            $us->setUserImage('//graph.facebook.com/'.$me->getId().'/picture?width=150&amp;height=150');

            $em = $this->getDoctrine()->getManager();
            $em->persist($us);
            $em->flush();

            $config = $this->getDoctrine()
                ->getRepository(Config::class)
                ->find(1);

            $token = new UsernamePasswordToken($us, null, 'main', $us->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $mailManager = new MailManagerUtils($emi);
            $mailBody = $this->renderView('emails/user_new_created.html.twig');
            $mailManager->sendEmail($mailBody,['subject' => 'Nowy użytkownik się zarejestrował - '.$config->getTitle()],$config->getEmail(),$mailer);

            $this->get('session')->set('_security_main', serialize($token));

            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        }

        return $this->redirectToRoute('index');
    }

    public function googleLogin(Request $request, \Swift_Mailer $mailer, EntityManagerInterface $emi, GoogleAuthenticator $google)
    {
        if (!session_id()) {
            session_start();
        }
        $dane = $google->googleLogin();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['email'=>$dane['email']],null,1);

        if(count($user) > 0){

            $user2 = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($user[0]->getId());
            if($user2->getIsActive()){
                $token = new UsernamePasswordToken($user2, null, 'main', $user2->getRoles());
                $this->get('security.token_storage')->setToken($token);

                $this->get('session')->set('_security_main', serialize($token));

                // Fire the login event manually
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                if($user2->getGoogleLogin()){

                }else{
                    $user2->setGoogleLogin(1);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user2);
                    $em->flush();
                }
            }else{
                $this->session->getFlashBag()->add('danger', 'Twoje konto jest wyłączone');

                return $this->redirectToRoute('index');
            }
        }else{
            $us = new User();
            $us->setEmail($dane['email']);
            $us->setFirstName($dane['given_name']);
            $us->setLastName($dane['family_name']);
            $us->setRegulations(1);
            $us->setRegulationFromRegister(1);
            $us->setMarketingRegulations(0);
            $us->setRoles(["ROLE_USER"]);
            $us->setIsActive(1);
            $us->setIsEnabledByAdmin(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($us);
            $em->flush();

            $config = $this->getDoctrine()
                ->getRepository(Config::class)
                ->find(1);

            $token = new UsernamePasswordToken($us, null, 'main', $us->getRoles());
            $this->get('security.token_storage')->setToken($token);

            $mailManager = new MailManagerUtils($emi);
            $mailBody = $this->renderView('emails/user_new_created.html.twig');
            $mailManager->sendEmail($mailBody,['subject' => 'Nowy użytkownik się zarejestrował - '.$config->getTitle()],$config->getEmail(),$mailer);

            $this->get('session')->set('_security_main', serialize($token));

            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
        }

        return $this->redirectToRoute('index');
    }
}
