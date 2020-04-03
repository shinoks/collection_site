<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TempOrderRepository")
 */
class TempOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="ilosc_akcji", type="integer")
     */
    private $ilosc_akcji;

    /**
     * @ORM\Column(name="kwota", type="float")
     */
    private $kwota;

    /**
     * @ORM\Column(name="data_zamowienia", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $data_zamowienia;

    /**
     * @ORM\Column(name="status", type="boolean", options={"default": 0})
     */
    private $status;

    /**
     * @ORM\Column(name="hash", type="string",length=255)
     */
    private $hash;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zbiorki", inversedBy="temp_orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zbiorka;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="temp_user_orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="temp")
     * @ORM\JoinColumn(nullable=false)
     */
    private $temp_order;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIloscAkcji()
    {
        return $this->ilosc_akcji;
    }

    /**
     * @param mixed $ilosc_akcji
     */
    public function setIloscAkcji($ilosc_akcji): void
    {
        $this->ilosc_akcji = $ilosc_akcji;
    }

    /**
     * @return mixed
     */
    public function getKwota()
    {
        return $this->kwota;
    }

    /**
     * @param mixed $kwota
     */
    public function setKwota($kwota): void
    {
        $this->kwota = $kwota;
    }

    /**
     * @return mixed
     */
    public function getDataZamowienia()
    {
        return $this->data_zamowienia;
    }

    /**
     * @param mixed $data_zamowienia
     */
    public function setDataZamowienia($data_zamowienia): void
    {
        $this->data_zamowienia = $data_zamowienia;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getZbiorka()
    {
        return $this->zbiorka;
    }

    /**
     * @param mixed $zbiorka
     */
    public function setZbiorka($zbiorka): void
    {
        $this->zbiorka = $zbiorka;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }
}
