<?php
namespace App\Service;

use Facebook\Exceptions\FacebookSDKException;

class Facebook
{
    private $siteUrl;
    private $facebookAppId;
    private $facebookAppSecret;

    public function __construct(string $siteUrl, string $facebookAppId, string $facebookAppSecret)
    {
        $this->siteUrl = $siteUrl;
        $this->facebookAppId = $facebookAppId;
        $this->facebookAppSecret = $facebookAppSecret;
    }


    public function fbLogin()
    {
        try {
            $fb = new \Facebook\Facebook([
                'app_id' => $this->facebookAppId,
                'app_secret' => $this->facebookAppSecret,
                'default_graph_version' => 'v2.2',
            ]);
        } catch (FacebookSDKException $e) {
        }

        $helper = $fb->getRedirectLoginHelper();

        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl($this->siteUrl.'/fb_callback', $permissions);


        return $loginUrl;
    }
}
