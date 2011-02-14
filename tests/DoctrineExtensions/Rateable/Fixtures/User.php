<?php

namespace Tests\DoctrineExtensions\Rateable\Fixtures;

use DoctrineExtensions\Rateable\Rateable;

/**
 * @Entity
 */
class User implements \DoctrineExtensions\Rateable\User
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    public $id = 0;

    /**
     * @Column(name="username", type="string", length=50)
     */
    public $username;

    public $canAddRate = true;
    public $canChangeRate = true;
    public $canRemoveRate = true;


    public function getId()
    {
        return $this->id;
    }


    public function canAddRate(Rateable $resource)
    {
        return $this->canAddRate;
    }

    public function canChangeRate(Rateable $resource)
    {
        return $this->canChangeRate;
    }

    public function canRemoveRate(Rateable $resource)
    {
        return $this->canRemoveRate;
    }
}
