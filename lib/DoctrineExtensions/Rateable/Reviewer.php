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
 * Reviewer is the interface that user entity must implement.
 *
 * @author Fabien Pennequin <fabien@pennequin.me>
 */
interface Reviewer
{
    /**
     * Returns unique identifiant
     *
     * @return integer An unique identifiant
     */
    function getReviewerId();

    /**
     * Check if user can add a rate for the resource
     *
     * @return Boolean TRUE if user can add a rate, FALSE otherwise.
     */
    function canAddRate(Rateable $resource);

    /**
     * Check if user can change his rate for the resource
     *
     * @return Boolean TRUE if user can change his rate, FALSE otherwise.
     */
    function canChangeRate(Rateable $resource);

    /**
     * Check if user can remove his rate for the resource
     *
     * @return Boolean TRUE if user can remove his rate, FALSE otherwise.
     */
    function canRemoveRate(Rateable $resource);
}
