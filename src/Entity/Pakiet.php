<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PakietRepository")
 */
class Pakiet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nazwa", type="string",length=255)
     */
    private $nazwa;

    /**
     * @ORM\Column(name="zawartosc", type="text")
     */
    private $zawartosc;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(name="ilosc_akcji", type="integer")
     */
    private $ilosc_akcji;

    /**
     * @ORM\Column(name="status", type="boolean", options={"default":0})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Zbiorki", inversedBy="pakiety")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zbiorka;

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
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getNazwa()
    {
        return $this->nazwa;
    }

    /**
     * @param mixed $nazwa
     */
    public function setNazwa($nazwa): void
    {
        $this->nazwa = $nazwa;
    }

    /**
     * @return mixed
     */
    public function getZawartosc()
    {
        return $this->zawartosc;
    }

    /**
     * @param mixed $zawartosc
     */
    public function setZawartosc($zawartosc): void
    {
        $this->zawartosc = $zawartosc;
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
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }
}
