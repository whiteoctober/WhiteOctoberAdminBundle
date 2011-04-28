<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Guesser;

class FieldOptionGuess
{

    const HIGH_CONFIDENCE   = 2;
    const MEDIUM_CONFIDENCE = 1;
    const LOW_CONFIDENCE    = 0;

    private static $confidences = array(
        self::HIGH_CONFIDENCE,
        self::MEDIUM_CONFIDENCE,
        self::LOW_CONFIDENCE,
    );

    private $optionName;
    private $optionValue;
    private $confidence;

    public function __construct($optionName, $optionValue, $confidence)
    {
        if (!in_array($confidence, self::$confidences, true)) {
            throw new \RuntimeException('The confidence is not valid.');
        }

        $this->optionName = $optionName;
        $this->optionValue = $optionValue;
        $this->confidence = $confidence;
    }

    public function getOptionName()
    {
        return $this->optionName;
    }

    public function getOptionValue()
    {
        return $this->optionValue;
    }

    public function getConfidence()
    {
        return $this->confidence;
    }
}
