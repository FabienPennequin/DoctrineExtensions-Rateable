<?php

/*
 * This file is part of the Doctrine Extensions Rateable package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineExtensions\Rateable;

use Doctrine\ORM\EntityManager;

/**
 * RatingManager.
 *
 * @author Fabien Pennequin <fabien@pennequin.me>
 */
class RatingManager
{
    protected $em;
    protected $minRateScore = 1;
    protected $maxRateScore = 5;

    public function __construct(EntityManager $em, $class = null)
    {
        $this->em = $em;
        $this->class = $class ?: 'DoctrineExtensions\Rateable\Entity\Rate';
    }

    /**
     * Adds a new rate.
     *
     * @param Rateable  $resource   The resource object
     * @param User      $resource   The user object
     * @param integer   $rateScore  The rate score
     *
     * @return void
     */
    public function addRate(Rateable $resource, User $user, $rateScore)
    {
        if (!$user->canAddRate($resource)) {
            throw new Exception\PermissionDeniedException('This user cannot add a rate for this resource');
        }

        if (!$this->isValidRateScore($rateScore)) {
            throw new Exception\InvalidRateScoreException($this->minRateScore, $this->maxRateScore);
        }

        if ($this->findRate($resource, $user)) {
            throw new Exception\ResourceAlreadyRatedException('The user has already rated this resource');
        }

        $rate = $this->createRate();
        $rate->setResource($resource);
        $rate->setUser($user);
        $rate->setScore($rateScore);
        $this->saveEntity($rate);

        $resource->setRatingVotes($resource->getRatingVotes() + 1);
        $resource->setRatingTotal($resource->getRatingTotal() + $rateScore);
        $this->saveEntity($resource);
    }

    /**
     * Changes score for an existant rate.
     *
     * @param Rateable  $resource   The resource object
     * @param User      $resource   The user object
     * @param integer   $rateScore  The new rate score
     *
     * @return void
     */
    public function changeRate(Rateable $resource, User $user, $rateScore)
    {
        if (!$user->canChangeRate($resource)) {
            throw new Exception\PermissionDeniedException('This user cannot change his rate for this resource');
        }

        if (!$this->isValidRateScore($rateScore)) {
            throw new Exception\InvalidRateScoreException($this->minRateScore, $this->maxRateScore);
        }

        if (!($rate = $this->findRate($resource, $user))) {
            throw new Exception\NotFoundRateException('Unable to find rate object');
        }

        $resource->setRatingTotal($resource->getRatingTotal() - $rate->getScore());
        $resource->setRatingTotal($resource->getRatingTotal() + $rateScore);

        $rate->setScore($rateScore);
        $rate->setUpdatedAt(new \DateTime('now'));
        $this->saveEntity($rate);

        $this->saveEntity($resource);
    }

    /**
     * Removes an existant rate.
     *
     * @param Rateable  $resource   The resource object
     * @param User      $resource   The user object
     *
     * @return void
     */
    public function removeRate(Rateable $resource, User $user)
    {
        if (!$user->canRemoveRate($resource)) {
            throw new Exception\PermissionDeniedException('This user cannot remove his rate for this resource');
        }

        if (!($rate = $this->findRate($resource, $user))) {
            throw new Exception\NotFoundRateException('Unable to find rate object');
        }

        $resource->setRatingVotes($resource->getRatingVotes() - 1);
        $resource->setRatingTotal($resource->getRatingTotal() - $rate->getScore());

        $this->em->remove($rate);
        $this->em->flush();

        $this->saveEntity($resource);
    }

    /**
     * Computes rating fields for an resource.
     *
     * @param Rateable  $resource   The resource object
     *
     * @return void
     */
    public function computeRating(Rateable $resource)
    {
        $votes = 0;
        $total = 0;

        foreach ($this->findRatesForResource($resource) as $rate) {
            $total += $rate->getScore();
            $votes++;
        }

        $resource->setRatingVotes($votes);
        $resource->setRatingTotal($total);
        $this->saveEntity($resource);
    }

    /**
     * Gets the rating score for a resource
     *
     * @param Rateable  $resource   The resource object
     * @param integer   $precision  Score precision
     *
     * @return void
     */
    public function getRatingScore(Rateable $resource, $precision=0)
    {
        if (!($resource->getRatingVotes() > 0)) {
            return 0;
        }

        return round($resource->getRatingTotal() / $resource->getRatingVotes(), $precision);
    }

    /**
     * Finds a rate object for a couple resource/user.
     *
     * @param Rateable  $resource   The resource object
     * @param User      $resource   The user object
     *
     * @return null|Rate The rate object if exist, null otherwise
     */
    public function findRate(Rateable $resource, User $user)
    {
        return $this->em
            ->getRepository($this->class)
            ->findOneBy(array(
                'resourceId'    => $resource->getResourceId(),
                'userId'        => $user->getId(),
            ))
        ;
    }

    /**
     * Finds all rate objects for a resource.
     *
     * @param Rateable  $resource   The resource object
     *
     * @return Doctrine\Common\Collection
     */
    public function findRatesForResource(Rateable $resource)
    {
        return $this->em
            ->getRepository($this->class)
            ->findBy(array(
                'resourceId'    => $resource->getResourceId(),
            ))
        ;
    }

    /**
     * Checks if a value is a valid rate score
     *
     * @param integer   $rateScore  The rate score
     *
     * @return Boolean TRUE if value is valid, FALSE otherwise
     */
    public function isValidRateScore($score)
    {
        return ($score >= $this->minRateScore
            and $score <= $this->maxRateScore);
    }

    /**
     * Returns the class name for Rate entity
     *
     * @return string
     */
    public function getRateClass()
    {
        return $this->class;
    }

    /**
     * Creates a new Rate object
     *
     * @return Rate
     */
    protected function createRate()
    {
        return new $this->class();
    }

    /**
     * Saves a Doctrine Entity
     *
     * @param mixed  $entity   The entity object
     *
     * @return mixed Entity object
     */
    protected function saveEntity($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }
}
