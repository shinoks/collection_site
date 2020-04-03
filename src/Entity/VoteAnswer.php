<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteAnswerRepository")
 */
class VoteAnswer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $answer;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\VoteQuestion", inversedBy="answers")
     * @ORM\JoinColumn(nullable=true)
     */
    private $voteQuestion2;
}
