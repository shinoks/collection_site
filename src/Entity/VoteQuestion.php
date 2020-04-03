<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteQuestionRepository")
 */
class VoteQuestion
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
    private $question;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vote", inversedBy="voteQuestion")
     * @ORM\JoinColumn(nullable=true)
     */
    private $vote;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\VoteAnswer", inversedBy="voteQuestion2")
     * @ORM\JoinColumn(nullable=true)
     */
    private $answers;
}
