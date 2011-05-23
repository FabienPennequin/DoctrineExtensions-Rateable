<?php

/*
 * This file is part of the Doctrine Extensions Rateable package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineExtensions\Rateable\Entity;

use DoctrineExtensions\Rateable\Rateable;
use DoctrineExtensions\Rateable\User;

class Rate
{
    protected $id;

    protected $resourceId;
    protected $userId;

    protected $score;

    protected $createdAt;
    protected $updatedAt;


    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->updatedAt = new \DateTime('now');
    }

    public function getId()
    {
        return $this->id;
    }

    public function setResource(Rateable $resource)
    {
        $this->resourceId = $resource->getResourceId();
    }

    public function getResourceId()
    {
        return $this->resourceId;
    }

    public function setUser(User $user)
    {
        $this->userId = $user->getId();
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function setCreatedAt(\DateTime $date)
    {
        $this->createdAt = $date;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $date)
    {
        $this->updatedAt = $date;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
