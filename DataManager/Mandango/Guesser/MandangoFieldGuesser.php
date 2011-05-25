<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Mandango\Guesser;

use WhiteOctober\AdminBundle\Guesser\FieldGuesserInterface;
use WhiteOctober\AdminBundle\Guesser\FieldOptionGuess;
use Mandango\MetadataFactory;

class MandangoFieldGuesser implements FieldGuesserInterface
{
    private $metadataFactory;

    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function guessOptions($class, $fieldName)
    {
        // the class is not a mandango document
        if (!$this->metadataFactory->hasClass($class)) {
            return array();
        }

        $options = array();

        $metadata = $this->metadataFactory->getClass($class);
        // fields
        if (isset($metadata['fields'][$fieldName])) {
            switch ($metadata['fields'][$fieldName]['type']) {
                case 'array':
                    break;
                case 'bin_data':
                    $options[] = new FieldOptionGuess(
                        'formType',
                        'file',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    break;
                case 'boolean':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/boolean.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'formType',
                        'checkbox',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'formOptions',
                        array('required' => false),
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    break;
                case 'date':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/date_time.html.twig',
                        FieldOptionGuess::MEDIUM_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'formType',
                        'datetime',
                        FieldOptionGuess::MEDIUM_CONFIDENCE
                    );
                    break;
                case 'float':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/float.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'formType',
                        'number',
                        FieldOptionGuess::MEDIUM_CONFIDENCE
                    );
                    break;
                case 'integer':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/integer.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'formType',
                        'number',
                        FieldOptionGuess::MEDIUM_CONFIDENCE
                    );
                    break;
                case 'string':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/text.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'formType',
                        'text',
                        FieldOptionGuess::MEDIUM_CONFIDENCE
                    );
                    break;
            }
        }

        return $options;
    }
}
