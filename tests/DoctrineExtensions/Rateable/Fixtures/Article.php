<?php

namespace Tests\DoctrineExtensions\Rateable\Fixtures;

/**
 * @Entity
 */
class Article implements \DoctrineExtensions\Rateable\Rateable
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    public $id = 0;

    /**
     * @Column(name="title", type="string", length=50)
     */
    public $title;

    /**
     * @Column(name="body", type="string")
     */
    public $body;

    /**
     * @Column(name="rating_votes", type="integer")
     */
    public $ratingVotes = 0;

    /**
     * @Column(name="rating_total", type="integer")
     */
    public $ratingTotal = 0;


    public function getId()
    {
        return $this->id;
    }

    public function getRatingVotes()
    {
        return $this->ratingVotes;
    }

    public function setRatingVotes($number)
    {
        $this->ratingVotes = $number;
    }

    public function getRatingTotal()
    {
        return $this->ratingTotal;
    }

    public function setRatingTotal($number)
    {
        $this->ratingTotal = $number;
    }
}
