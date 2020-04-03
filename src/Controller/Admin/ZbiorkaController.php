<?php
namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Zbiorki;
use App\Form\ZbiorkiType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Form\CategoryType;

class ZbiorkaController extends Controller
{
    /**
     * @var Session
     */
    private $session;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @return Response
     */
    public function zbiorki(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Zbiorki::class)->findBy([],[
            $request->query->get('sort','id')=>$request->query->get('direction','desc')
        ]);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)
        );

        return $this->render('back/zbiorki.html.twig',array(
            'pagination'=> $pagination
        ));
    }

    /**
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        $zbiorki = $this->getDoctrine()
            ->getRepository(Zbiorki::class)
            ->find($id);

        return $this->render('back/zbiorka_show.html.twig',array(
            'zbiorki'=> $zbiorki
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function new(Request $request){

        $zbiorki = new Zbiorki();
        $form = $this->createForm(ZbiorkiType::class, $zbiorki);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($zbiorki);
            $em->flush();

            $this->session->getFlashBag()->add('success', 'Zbiórka została zmieniona');

            return $this->redirectToRoute('admin_zbiorka_edit',array('id'=>$zbiorki->getId()));
        }

        return $this->render('back/zbiorka_new.html.twig',array(
            'form'=> $form->createView()
        ));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function edit(int $id, Request $request)    {
        $zbiorki = $this->getDoctrine()
            ->getRepository(Zbiorki::class)
            ->find($id);
        if($zbiorki){
            $form = $this->createForm(ZbiorkiType::class, $zbiorki);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $zbiorki = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($zbiorki);
                $em->flush();

                $this->session->getFlashBag()->add('success', 'Zbiórka została zmieniona');

                return $this->render('back/zbiorka_edit.html.twig',array(
                    'category'=> $zbiorki,
                    'form'=> $form->createView()
                ));
            }

            return $this->render('back/zbiorka_edit.html.twig',array(
                'category'=> $zbiorki,
                'form'=> $form->createView()
            ));
        }else {

            $this->session->getFlashBag()->add('danger', 'Zbiórka nie została znaleziona');

            return $this->redirectToRoute('admin_zbiorki');
        }
    }

    /**
     * @param int $id
     * @param $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disable(int $id, $status)    {
        $zbiorki = $this->getDoctrine()
            ->getRepository(Zbiorki::class)
            ->find($id);
        $zbiorki->setStatus($status);

        $em = $this->getDoctrine()->getManager();
        $em->persist($zbiorki);
        $em->flush();

        $this->session->getFlashBag()->add('success', 'Zbiórki została wyłączona');

        return $this->redirectToRoute('admin_zbiorki');
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(int $id)    {
        $zbiorki = $this->getDoctrine()
            ->getRepository(Zbiorki::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($zbiorki);
        $em->flush();

        $this->session->getFlashBag()->add('success', 'Zbiórka została usunięta');

        return $this->redirectToRoute('admin_zbiorki');
    }
}
