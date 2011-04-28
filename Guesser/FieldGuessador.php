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

class FieldGuessador
{
    private $guessers;

    public function __construct(array $guessers)
    {
        $this->guessers = array();
        foreach ($guessers as $guesser) {
            $this->add($guesser);
        }
    }

    public function add(FieldGuesserInterface $guesser)
    {
        $this->guessers[] = $guesser;
    }

    public function all()
    {
        return $this->guessers;
    }

    public function guessOptions($class, $fieldName)
    {
        $optionGuesses = array();
        foreach ($this->guessers as $guesser) {
            $guesses = $guesser->guessOptions($class, $fieldName);
            foreach ($guesses as $guess) {
                $optionGuesses[$guess->getOptionName()][] = $guess;
            }
        }

        $options = array();
        foreach ($optionGuesses as $name => $guesses) {
            $value = null;
            $confidence = 0;
            foreach ($guesses as $guess) {
                if ($guess->getConfidence() >= $confidence) {
                    $value = $guess->getOptionValue();
                    $confidence = $guess->getConfidence();
                }
            }
            $options[$name] = $value;
        }

        return $options;
    }
}
