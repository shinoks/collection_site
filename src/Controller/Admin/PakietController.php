<?php
namespace App\Controller\Admin;

use App\Entity\Pakiet;
use App\Form\PakietType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class PakietController extends Controller
{
    /**
     * @var Session
     */
    private $session;

    /**
     * ArticleController constructor.
     */
    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        $pakiet = $this->getDoctrine()
            ->getRepository(Pakiet::class)
            ->find($id);

        return $this->render('back/pakiet_show.html.twig',array(
            'article'=> $pakiet
        ));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(int $id, Request $request)    {
        $pakiet = $this->getDoctrine()
            ->getRepository(Pakiet::class)
            ->find($id);


        $imageOld = $pakiet->getImage();
        if($pakiet){
            $form = $this->createForm(PakietType::class, $pakiet);
            if($imageOld){
                $pakiet->setImage($imageOld);
            }
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $pakiet = $form->getData();
                if(!$pakiet->getImage()){
                    $pakiet->setImage($imageOld);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($pakiet);
                $em->flush();

                $this->session->getFlashBag()->add('success', 'Pakiet został zmieniony');

                return $this->redirectToRoute('admin_pakiet_edit',['id'=>$id]);
            }

            return $this->render('back/pakiet_edit.html.twig',array(
                'pakiet'=> $pakiet,
                'form'=> $form->createView()
            ));
        }else {
            $this->session->getFlashBag()->add('danger', 'Artykuł nie został znaleziony');

            return $this->redirectToRoute('admin_articles');
        }

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function new(Request $request){

        $pakiet = new Pakiet();
        $form = $this->createForm(PakietType::class, $pakiet);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($pakiet);
            $em->flush();

            $this->session->getFlashBag()->add('success', 'Pakiet został zmieniony');

            return $this->redirectToRoute('admin_pakiet_edit',array('id'=>$pakiet->getId()));
        }

        return $this->render('back/pakiet_new.html.twig',array(
            'form'=> $form->createView()
        ));
    }

    /**
     * @param int $id
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disable(int $id, int $status)    {
        $pakiet = $this->getDoctrine()
            ->getRepository(Pakiet::class)
            ->find($id);
        $pakiet->setStatus($status);

        $em = $this->getDoctrine()->getManager();
        $em->persist($pakiet);
        $em->flush();

        if($status == 1){
            $this->session->getFlashBag()->add('success', 'Pakiet został włączony');
        }else {
            $this->session->getFlashBag()->add('success', 'Pakiet został wyłączony');
        }

        return $this->redirectToRoute('admin_pakiety');
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(int $id)    {
        $pakiet = $this->getDoctrine()
            ->getRepository(Pakiet::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($pakiet);
        $em->flush();

        $this->session->getFlashBag()->add('success', 'Pakiet został usunięty');

        return $this->redirectToRoute('admin_pakiety');
    }

    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Pakiet::class)->findBy([],[
            $request->query->get('sort','id')=>$request->query->get('direction','asc')
        ]);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)
        );

        return $this->render('back/pakiety.html.twig',array(
            'pagination'=> $pagination
        ));
    }
}
