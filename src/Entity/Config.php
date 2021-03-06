<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $logoMain;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $logoAdmin;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $regulationsUrl;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $privacyPolicyUrl;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bankAccount;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $keywords;

    /**
     * @ORM\Column(type="text")
     */
    private $footer;

    /**
     * @ORM\Column(name="startArticlesColumn", type="integer")
     */
    private $startArticlesColumn;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getRegulationsUrl()
    {
        return $this->regulationsUrl;
    }

    /**
     * @param mixed $regulationsUrl
     */
    public function setRegulationsUrl($regulationsUrl)
    {
        $this->regulationsUrl = $regulationsUrl;
    }

    /**
     * @return mixed
     */
    public function getPrivacyPolicyUrl()
    {
        return $this->privacyPolicyUrl;
    }

    /**
     * @param mixed $privacyPolicyUrl
     */
    public function setPrivacyPolicyUrl($privacyPolicyUrl): void
    {
        $this->privacyPolicyUrl = $privacyPolicyUrl;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param mixed $bankAccount
     */
    public function setBankAccount($bankAccount): void
    {
        $this->bankAccount = $bankAccount;
    }

    /**
     * @return mixed
     */
    public function getLogoMain()
    {
        return $this->logoMain;
    }

    /**
     * @param mixed $logoMain
     */
    public function setLogoMain($logoMain)
    {
        $this->logoMain = $logoMain;
    }

    /**
     * @return mixed
     */
    public function getLogoAdmin()
    {
        return $this->logoAdmin;
    }

    /**
     * @param mixed $logoAdmin
     */
    public function setLogoAdmin($logoAdmin)
    {
        $this->logoAdmin = $logoAdmin;
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return mixed
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param mixed $footer
     */
    public function setFooter($footer): void
    {
        $this->footer = $footer;
    }

    /**
     * @return int
     */
    public function getStartArticlesColumn(): int
    {
        return $this->startArticlesColumn;
    }

    /**
     * @param mixed $startArticlesColumn
     */
    public function setStartArticlesColumn($startArticlesColumn): void
    {
        $this->startArticlesColumn = $startArticlesColumn;
    }

}
