<?php
namespace App\Controller\Admin;

use App\Entity\RecruitmentUsers;
use App\Entity\Subscriber;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function index()
    {
        $usersCount = $this->getDoctrine()
            ->getRepository(User::class)
            ->count();

        $recruitmentUsersCount = $this->getDoctrine()
            ->getRepository(RecruitmentUsers::class)
            ->count();

        $recruitmentUsersSumDeclaredAmount = $this->getDoctrine()
            ->getRepository(RecruitmentUsers::class)
            ->sumDeclaredAmount();

        $recruitmentUsersSumPayedAmount = $this->getDoctrine()
            ->getRepository(RecruitmentUsers::class)
            ->sumPayedAmount();

        $subscriberCount = $this->getDoctrine()
            ->getRepository(Subscriber::class)
            ->count();

        return $this->render('back/start.html.twig',[
            'usersCount' => $usersCount,
            'recruitmentUsersCount' => $recruitmentUsersCount,
            'recruitmentUsersSumDeclaredAmount' => $recruitmentUsersSumDeclaredAmount,
            'recruitmentUsersSumPayedAmount' => $recruitmentUsersSumPayedAmount,
            'subscriberCount' => $subscriberCount
        ]);
    }

}
