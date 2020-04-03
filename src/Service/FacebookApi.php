<?php
namespace App\Service;

/**
 * Class FacebookApi
 * @package LukaszSocha\DemoFB
 * @author Łukasz Socha <kontakt@lukasz-socha.pl>
 * @licence http://opensource.org/licenses/bsd-license.php
 */
class FacebookApi{
    /**
     * @var string ID aplikacji
     */
    private $appId;
    /**
     * @var string Klucz secret do aplikacji
     */
    private $secret;
    /**
     * @var Facebook API
     */
    private $fbSdk;
    /**
     * @var array Lista praw dostępu. Pełna lista dostępna tutaj https://developers.facebook.com/docs/facebook-login/permissions
     */
    private $permissions=array();
    /**
     * @var string Adres URL na jaki przekieruje po zalogowaniu
     */
    private $callback;

    /**
     * @param string $appId
     * @param string $secret
     */
    public function __construct($appId, $secret)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->fbSdk=new \Facebook\Facebook(array('app_id' => $this->appId, 'app_secret' => $this->secret));
    }

    /**
     * @param array $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param string $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Zwraca access token, jeżeli istnieje
     * @return bool|string
     */
    public function getAccessToken() {
        if(!empty($_SESSION['facebook_access_token'])) {
            return $_SESSION['facebook_access_token'];
        }
        return false;
    }

    /**
     * Pobiera access token zalogowanego użytkownika
     * @return bool
     */
    public function login() {
        $helper = $this->fbSdk->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // There was an error communicating with Graph
            echo $e->getMessage();
            exit;
        }

        if (isset($accessToken)) {
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            echo 'Successfully logged in!';
            exit;
        } elseif ($helper->getError()) {
            var_dump($helper->getError());
            var_dump($helper->getErrorCode());
            var_dump($helper->getErrorReason());
            var_dump($helper->getErrorDescription());
            exit;
        }
    }

    /**
     * Zwraca adres URL do zalogowania
     * @return string adres URL
     */
    public function getLoginUrl() {
        $helper = $this->fbSdk->getRedirectLoginHelper();
        return $helper->getLoginUrl($this->callback, $this->permissions);
    }

    /**
     * Zwraca tablicę z danymi użytkownika
     * @param string $param Informacje jakie mają byc pobrane z Facebooka
     * @return array Dane użytkownika
     */
    public function getUserData($param) {
        return $this->fbSdk->get($param, $this->getAccessToken());
    }
}
