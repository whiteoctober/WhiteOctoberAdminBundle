<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Doctrine\ODM\Guesser;

use WhiteOctober\AdminBundle\Guesser\FieldGuesserInterface;
use WhiteOctober\AdminBundle\Guesser\FieldOptionGuess;
use Doctrine\ODM\MongoDB\DocumentManager;

class DoctrineODMFieldGuesser implements FieldGuesserInterface
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function guessOptions($class, $fieldName)
    {
        $options = array();

        return $options;
    }
}
