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

/**
 * @orm:Entity
 */
class Rate
{
    /**
     * @orm:Id
     * @orm:GeneratedValue
     * @orm:Column(type="integer")
     */
    protected $id;

    /**
     * @orm:Column(name="resource_name", type="string")
     */
    protected $resourceName;

    /**
     * @orm:Column(name="resource_id", type="integer")
     */
    protected $resourceId;

    /**
     * @orm:Column(name="user_id", type="integer")
     */
    protected $userId;

    /**
     * @orm:Column(name="score", type="integer")
     */
    protected $score;

    /**
     * @orm:Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @orm:Column(name="updated_at", type="datetime")
     */
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
        $this->resourceName = static::findResourceName($resource);
        $this->resourceId = static::findResourceId($resource);
    }

    public function getResourceName()
    {
        return $this->resourceName;
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


    public static function findResourceName(Rateable $resource)
    {
        return get_class($resource);
    }

    public static function findResourceId(Rateable $resource)
    {
        return $resource->getId();
    }
}
