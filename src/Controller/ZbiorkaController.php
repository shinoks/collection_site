<?php
namespace App\Controller;

use App\Entity\Zbiorki;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ZbiorkaController extends Controller
{

    /**
     * @return Response
     */
    public function renderModulePakiety($idZbiorki,$moduleName)
    {
        $zbiorka = $this->getDoctrine()
            ->getRepository(Zbiorki::class)
            ->find($idZbiorki);

        return $this->render('front/modules/commision_packages.html.twig',array(
            'zbiorka'=> $zbiorka,
            'moduleName'=> $moduleName
        ));
    }
}
