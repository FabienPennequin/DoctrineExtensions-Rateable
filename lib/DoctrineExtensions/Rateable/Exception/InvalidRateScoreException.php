<?php

/*
 * This file is part of the Doctrine Extensions Rateable package.
 * (c) 2011 Fabien Pennequin <fabien@pennequin.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DoctrineExtensions\Rateable\Exception;

class InvalidRateScoreException extends \Exception
{
    public function __construct($minValue, $maxValue, $code = null)
    {
        parent::__construct(
            sprintf('Rate score must be between %d and %d', $minValue, $maxValue),
            $code
        );
    }
}
