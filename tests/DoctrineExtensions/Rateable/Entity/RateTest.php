<?php

/*
 * This file is part of the Doctrine Extensions Rateable package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../Fixtures/Article.php';
require_once __DIR__.'/../Fixtures/User.php';

use DoctrineExtensions\Rateable\Rateable;
use DoctrineExtensions\Rateable\Entity\Rate;
use Tests\DoctrineExtensions\Rateable\Fixtures\User;


class RateTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $rate = new Rate();
        $this->assertEquals(new \DateTime('now'), $rate->getCreatedAt());
        $this->assertEquals(new \DateTime('now'), $rate->getUpdatedAt());
    }

    /**
     * @covers DoctrineExtensions\Rateable\Entity\Rate::setResource
     * @covers DoctrineExtensions\Rateable\Entity\Rate::getResourceName
     * @covers DoctrineExtensions\Rateable\Entity\Rate::getResourceId
     */
    public function testSetGetResource()
    {
        $article = new \Tests\DoctrineExtensions\Rateable\Fixtures\Article();
        $article->id = 123;

        $rate = new Rate();
        $rate->setResource($article);

        $this->assertEquals(123, $rate->getResourceId());
        $this->assertEquals('Tests\\DoctrineExtensions\\Rateable\\Fixtures\\Article', $rate->getResourceName());
    }

    /**
     * @covers DoctrineExtensions\Rateable\Entity\Rate::setUser
     * @covers DoctrineExtensions\Rateable\Entity\Rate::getUserId
     */
    public function testSetGetUser()
    {
        $user = new User();
        $user->id = 345;

        $rate = new Rate();
        $rate->setUser($user);

        $this->assertEquals(345, $rate->getUserId());
    }
}
