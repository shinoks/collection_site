<?php
namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\UserType;
use App\Service\Facebook;
use App\Service\GoogleAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils, Facebook $fb,GoogleAuthenticator $google)
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find(4);

        $fbLink = $fb->fbLogin();
        $googleLink = $google->getGoogleLoginLink();

        return $this->render('front/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'session' => $this->session,
            'form_register' => $form->createView(),
            'article' => $article,
            'fbLink' => $fbLink,
            'googleLink' => $googleLink
        ));
    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function adminLogin(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('back/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
}
