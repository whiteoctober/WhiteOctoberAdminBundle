<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ORM\Guesser;

use WhiteOctober\AdminBundle\Guesser\FieldGuesserInterface;
use WhiteOctober\AdminBundle\Guesser\FieldOptionGuess;
use Doctrine\ORM\EntityManager;

class DoctrineORMFieldGuesser implements FieldGuesserInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function guessOptions($class, $fieldName)
    {
        $options = array();

        return $options;
    }
}
