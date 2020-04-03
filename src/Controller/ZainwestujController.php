<?php
namespace App\Controller;

use App\Entity\Config;
use App\Entity\TempOrder;
use App\Entity\Zbiorki;
use App\Form\ContactType;
use App\Service\Facebook;
use App\Service\Przelewy24;
use App\Utils\RecaptchaUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Entity\Contact;
use App\Entity\Realization;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class ZainwestujController extends Controller
{
    /**
     * @var Session
     */
    private $session;
    private $cenaAkcji;

    /**
     * DefaultController constructor.
     */
    public function __construct()
    {
        $this->session = new Session();
        $this->cenaAkcji = 1;
    }

    /**
     * @return Response
     */
    public function investPage($idZbiorki = 1)
    {
        $cenaAkcji = $this->cenaAkcji;
        $zbiorka = $this->getDoctrine()
            ->getRepository(Zbiorki::class)
            ->findOneBy(['id'=>$idZbiorki]);
        $kwotaBrakujaca = $zbiorka->getKwotaZbiorki() - $zbiorka->getKwotaZebrana();
        $procentZebrany = round(($zbiorka->getKwotaZebrana()/$zbiorka->getKwotaZbiorki())*100,2);
        $kwotaMin = 100;
        $kwotaStart = 1000;
        $kwotaKrok = 100;

        return $this->render('front/invest.html.twig',array(
            'cenaAkcji' => $cenaAkcji,
            'zbiorka' =>$zbiorka,
            'kwotaBrakujaca' => $kwotaBrakujaca,
            'kwotaMin' => $kwotaMin,
            'kwotaStart' => $kwotaStart,
            'kwotaKrok' => $kwotaKrok,
            'procentZebrany' => $procentZebrany
        ));
    }

    /**
     * @return Response
     */
    public function investPayPage()
    {
        $hash = password_hash(md5(time()),PASSWORD_DEFAULT);
        $merchantId = '';
        $kluczZakupu = '';
        $nrTransakcji = '';
        $crc = '';
        $dodatkowyOpis = '';
        $iloscAkcji = $_POST['iloscAkcji'];
        $cenaAkcji = $this->cenaAkcji;
        $kwotaDoZaplaty = $iloscAkcji*$cenaAkcji;
        $przelewy24 = new Przelewy24(0,0,0,true);

        $tytulPlatnosci='';
        $adresPowrotu='';
        $kwota='';
        $opis='';
        $przelewy24->addValue('z24_nazwa',$tytulPlatnosci);
        $przelewy24->addValue('z24_return_url',$adresPowrotu);
        $przelewy24->addValue('z24_kwota',$kwota);
        $przelewy24->addValue('z24_opis',$opis);

        $nazwaKlienta = '';
        $emailKlienta = '';
        $kodKrajuKlienta = 'pl';
        $kodKlienta = '';
        $miastoKlienta = '';
        $ulicaKlienta = '';
        $numerDomuKlienta = '';
        $numerLokaluKlienta = '';
        $przelewy24->addValue('k24_nazwa',$nazwaKlienta);
        $przelewy24->addValue('k24_email',$emailKlienta);
        $przelewy24->addValue('k24_kraj',$kodKrajuKlienta);
        $przelewy24->addValue('k24_kod',$kodKlienta);
        $przelewy24->addValue('k24_miasto',$miastoKlienta);
        $przelewy24->addValue('k24_ulica',$ulicaKlienta);
        $przelewy24->addValue('k24_numer_dom',$numerDomuKlienta);
        $przelewy24->addValue('k24_numer_lok',$numerLokaluKlienta);

        //var_dump($przelewy24->testConnection());
        return $this->render('front/invest_pay.html.twig',array(
            'cenaAkcji' => $cenaAkcji,
            'iloscAkcji' => $iloscAkcji,
            'kwotaDoZaplaty' => $kwotaDoZaplaty,
            'merchantId' => $merchantId,
            'crc' => $crc,
            'nrTransakcji' => $nrTransakcji,
            'dodatkowyOpis' => $dodatkowyOpis
        ));
    }

    public function makeTempOrder($iloscAkcji, $cenaAkcji, $zbiorka,$hash)
    {
        $tempOrder = new TempOrder();
        $tempOrder->setIloscAkcji($iloscAkcji);
        $tempOrder->setUser($this->getUser());
        $tempOrder->setKwota($iloscAkcji*$cenaAkcji);
        $tempOrder->setStatus(0);
        $tempOrder->setZbiorka($zbiorka);
        $tempOrder->setHash($hash);

        $em = $this->getDoctrine()->getManager();
        $em->persist($tempOrder);
        $em->flush();

        return $tempOrder->getId();
    }
}
