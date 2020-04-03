<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ZbiorkiRepository")
 */
class Zbiorki
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nazwa", type="string", length=150)
     */
    private $nazwa;

    /**
     * @ORM\Column(name="kwota_zbiorki", type="float")
     */
    private $kwota_zbiorki;

    /**
     * @ORM\Column(name="kwota_zebrana", type="float", options={"default":0})
     */
    private $kwota_zebrana;

    /**
     * @ORM\Column(name="start_zbiorki", type="datetime", nullable=true, options={"default":NULL})
     */
    private $start_zbiorki;

    /**
     * @ORM\Column(name="koniec_zbiorki", type="datetime", nullable=true, options={"default":NULL})
     */
    private $koniec_zbiorki;

    /**
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TempOrder", mappedBy="zbiorka")
     */
    private $temp_orders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="zbiorka")
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pakiet", mappedBy="zbiorka")
     */
    private $pakiety;

    public function __construct()
    {
    }

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
    public function getKwotaZbiorki()
    {
        return $this->kwota_zbiorki;
    }

    /**
     * @param mixed $kwota_zbiorki
     */
    public function setKwotaZbiorki($kwota_zbiorki): void
    {
        $this->kwota_zbiorki = $kwota_zbiorki;
    }

    /**
     * @return mixed
     */
    public function getKwotaZebrana()
    {
        return $this->kwota_zebrana;
    }

    /**
     * @param mixed $kwota_zebrana
     */
    public function setKwotaZebrana($kwota_zebrana): void
    {
        $this->kwota_zebrana = $kwota_zebrana;
    }

    /**
     * @return mixed
     */
    public function getStartZbiorki()
    {
        return $this->start_zbiorki;
    }

    /**
     * @param mixed $start_zbiorki
     */
    public function setStartZbiorki($start_zbiorki): void
    {
        $this->start_zbiorki = $start_zbiorki;
    }

    /**
     * @return mixed
     */
    public function getKoniecZbiorki()
    {
        return $this->koniec_zbiorki;
    }

    /**
     * @param mixed $koniec_zbiorki
     */
    public function setKoniecZbiorki($koniec_zbiorki): void
    {
        $this->koniec_zbiorki = $koniec_zbiorki;
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
    public function getTempOrders()
    {
        return $this->temp_orders;
    }

    /**
     * @param mixed $temp_orders
     */
    public function setTempOrders($temp_orders): void
    {
        $this->temp_orders = $temp_orders;
    }

    /**
     * @return mixed
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param mixed $orders
     */
    public function setOrders($orders): void
    {
        $this->orders = $orders;
    }

    /**
     * @return mixed
     */
    public function getPakiety()
    {
        return $this->pakiety;
    }

    /**
     * @param mixed $pakiety
     */
    public function setPakiety($pakiety): void
    {
        $this->pakiety = $pakiety;
    }

}
