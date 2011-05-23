<?php

/*
 * This file is part of the Doctrine Extensions Rateable package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/Fixtures/Article.php';
require_once __DIR__.'/Fixtures/User.php';
require_once __DIR__.'/Fixtures/Rate.php';

use DoctrineExtensions\Rateable\RatingManager;
use DoctrineExtensions\Rateable\RatingListener;
use Tests\DoctrineExtensions\Rateable\Fixtures\User;
use Tests\DoctrineExtensions\Rateable\Fixtures\Article;

class RatingManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $manager;
    protected $article;

    public function setUp()
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
        $config->setProxyDir(__DIR__ . '/Proxy');
        $config->setProxyNamespace('DoctrineExtensions\Rateable\Proxies');
        //$config->setMetadataDriverImpl($config->newDefaultAnnotationDriver());

        $driverImpl = new \Doctrine\ORM\Mapping\Driver\DriverChain();
        $driverImpl->addDriver(new \Doctrine\ORM\Mapping\Driver\XmlDriver(__DIR__.'/../../../metadata'), 'DoctrineExtensions\\Rateable\\Entity');
        $driverImpl->addDriver($config->newDefaultAnnotationDriver(), 'Tests\\DoctrineExtensions\\Rateable\\Fixtures');
        $config->setMetadataDriverImpl($driverImpl);

        $this->em = \Doctrine\ORM\EntityManager::create(
            array('driver' => 'pdo_sqlite', 'memory' => true),
            $config
        );

        $this->em->getEventManager()->addEventSubscriber(new RatingListener());

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $schemaTool->dropSchema(array());
        $schemaTool->createSchema(array(
            $this->em->getClassMetadata('DoctrineExtensions\\Rateable\\Entity\\Rate'),
            $this->em->getClassMetadata('Tests\\DoctrineExtensions\\Rateable\\Fixtures\\Article'),
            $this->em->getClassMetadata('Tests\\DoctrineExtensions\\Rateable\\Fixtures\\User'),
        ));

        $article1 = new Article();
        $article1->title = 'Unit Testing';
        $article1->body = 'My first article about unit testing';
        $this->em->persist($article1);

        $this->article = new Article();
        $this->article->title = 'Unit Testing #2';
        $this->article->body = 'My second article about unit testing';
        $this->em->persist($this->article);

        $user1 = new User();
        $user1->username = 'Fabien';
        $this->em->persist($user1);

        $user2 = new User();
        $user2->username = 'Fabien2';
        $this->em->persist($user2);

        $this->em->flush();

        $this->manager = new RatingManager($this->em);
        $this->manager->addRate($article1, $user1, 5);
        $this->manager->addRate($this->article, $user1, 3);
        $this->manager->addRate($this->article, $user2, 2);
    }

    public function testConstructor()
    {
        $manager = new RatingManager($this->em);
        $this->assertInstanceOf('DoctrineExtensions\Rateable\RatingManager', $manager);

        $class = 'Tests\DoctrineExtensions\Rateable\Fixtures\Rate';
        $manager = new RatingManager($this->em, $class);
        $this->assertEquals($class, $manager->getRateClass());
    }

    public function testAddRate()
    {
        $user = new User();
        $user->id = 123;

        $this->assertEquals(2, $this->article->getRatingVotes());
        $this->assertEquals(5, $this->article->getRatingTotal());

        $this->manager->addRate($this->article, $user, 4);
        $this->assertEquals(3, $this->article->getRatingVotes());
        $this->assertEquals(9, $this->article->getRatingTotal());

        $rate = $this->manager->findRate($this->article, $user);
        $this->assertEquals($this->article->getId(), $rate->getResourceId());
        $this->assertEquals(4, $rate->getScore());
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\PermissionDeniedException
     */
    public function testAddRateWithoutPermission()
    {
        $user = new User();
        $user->id = 456;
        $user->canAddRate = false;

        $this->manager->addRate($this->article, $user, 2);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\InvalidRateScoreException
     */
    public function testAddRateWithScoreLessThanOne()
    {
        $this->manager->addRate(new Article(), new User(), 0);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\InvalidRateScoreException
     */
    public function testAddRateWithScoreMoreThanFive()
    {
        $this->manager->addRate(new Article(), new User(), 6);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\ResourceAlreadyRatedException
     */
    public function testAddRateAgain()
    {
        $user = new User();
        $user->id = 123;

        $this->manager->addRate($this->article, $user, 4);
        $this->manager->addRate($this->article, $user, 4);
    }


    public function testChangeRate()
    {
        $user = new User();
        $user->id = 123;

        $this->manager->addRate($this->article, $user, 4);
        $this->assertEquals(4, $this->manager->findRate($this->article, $user)->getScore());
        $this->assertEquals(9, $this->article->getRatingTotal());

        sleep(1);
        $this->manager->changeRate($this->article, $user, 2);
        $this->assertEquals(3, $this->article->getRatingVotes());
        $this->assertEquals(7, $this->article->getRatingTotal());

        $rate = $this->manager->findRate($this->article, $user);
        $this->assertEquals(2, $rate->getScore());
        $this->assertEquals(new \DateTime('now'), $rate->getUpdatedAt());
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\PermissionDeniedException
     */
    public function testChangeRateWithoutPermission()
    {
        $user = new User();
        $user->id = 456;
        $user->canChangeRate = false;

        $this->manager->changeRate($this->article, $user, 2);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\InvalidRateScoreException
     */
    public function testChangeRateWithScoreLessThanOne()
    {
        $this->manager->changeRate(new Article(), new User(), 0);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\InvalidRateScoreException
     */
    public function testChangeRateWithScoreMoreThanFive()
    {
        $this->manager->changeRate(new Article(), new User(), 6);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\NotFoundRateException
     */
    public function testChangeRateWithInvalidRate()
    {
        $user = new User();
        $user->id = 123;

        $article = new Article();
        $article->id = 456;

        $this->manager->changeRate($article, $user, 2);
    }


    public function testRemoveRate()
    {
        $user = new User();
        $user->id = 123;

        $this->manager->addRate($this->article, $user, 4);
        $this->assertEquals(4, $this->manager->findRate($this->article, $user)->getScore());
        $this->assertEquals(3, $this->article->getRatingVotes());

        $this->manager->removeRate($this->article, $user);
        $this->assertNull($this->manager->findRate($this->article, $user));
        $this->assertEquals(2, $this->article->getRatingVotes());
        $this->assertEquals(5, $this->article->getRatingTotal());
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\PermissionDeniedException
     */
    public function testRemoveRateWithoutPermission()
    {
        $user = new User();
        $user->id = 456;
        $user->canRemoveRate = false;

        $this->manager->removeRate($this->article, $user, 2);
    }

    /**
     * @expectedException DoctrineExtensions\Rateable\Exception\NotFoundRateException
     */
    public function testRemoveRateWithInvalidRate()
    {
        $user = new User();
        $user->id = 123;

        $article = new Article();
        $article->id = 456;

        $this->manager->removeRate($article, $user);
    }


    public function testComputeRating()
    {
        $this->article->ratingVotes = 10;
        $this->article->ratingTotal = 25;

        $this->manager->computeRating($this->article);
        $this->assertEquals(2, $this->article->getRatingVotes());
        $this->assertEquals(5, $this->article->getRatingTotal());
    }

    public function testGetRatingScore()
    {
        $this->assertEquals(3., $this->manager->getRatingScore($this->article));
        $this->assertEquals(2.5, $this->manager->getRatingScore($this->article, 1));

        $this->article->ratingVotes = 0;
        $this->article->ratingTotal = 0;
        $this->assertEquals(0, $this->manager->getRatingScore($this->article));
    }

    public function testIsValidRateScore()
    {
        $this->assertTrue($this->manager->isValidRateScore(1));
        $this->assertTrue($this->manager->isValidRateScore(5));
        $this->assertFalse($this->manager->isValidRateScore(0));
        $this->assertFalse($this->manager->isValidRateScore(6));
    }

    public function testDeleteResource()
    {
        $this->assertEquals(2, sizeof($this->manager->findRatesForResource($this->article)));

        $article = clone $this->article;
        $this->em->remove($this->article);
        $this->em->flush();

        $this->assertEquals(0, sizeof($this->manager->findRatesForResource($article)));
    }
}
