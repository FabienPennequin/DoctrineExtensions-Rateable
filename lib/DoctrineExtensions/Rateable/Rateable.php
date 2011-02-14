<?php

/*
 * This file is part of the Doctrine Extensions Rateable package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineExtensions\Rateable;

/**
 * Rateable is the interface that rateable resource classes must implement.
 *
 * @author Fabien Pennequin <fabien@pennequin.me>
 */
interface Rateable
{
    function getId();

    function getRatingVotes();
    function setRatingVotes($number);

    function getRatingTotal();
    function setRatingTotal($number);

    /**
     * Returns all rates for this entity.
     *
     * @return Doctrine\Common\Collections\Collection An doctrine collection
     */
    #function getRating();
}
