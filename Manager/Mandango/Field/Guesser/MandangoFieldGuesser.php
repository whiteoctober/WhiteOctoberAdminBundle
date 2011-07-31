<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\Manager\Mandango\Field\Guesser;

use Pablodip\ModuleBundle\Field\Guesser\FieldGuesserInterface;
use Pablodip\ModuleBundle\Field\Guesser\FieldOptionGuess;
use Mandango\MetadataFactory;

/**
 * MandangoFieldGuesser.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MandangoFieldGuesser implements FieldGuesserInterface
{
    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param MetadataFactory $metadataFactory The Mandango metadata factory.
     */
    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
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
                    break;
                case 'boolean':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/boolean.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'advanced_filter_type',
                        'boolean',
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
                        'advanced_filter_type',
                        'date',
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
                        'advanced_filter_type',
                        'float',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    break;
                case 'integer':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/integer.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'advanced_filter_type',
                        'integer',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    break;
                case 'string':
                    $options[] = new FieldOptionGuess(
                        'template',
                        'WhiteOctoberAdminBundle::fields/text.html.twig',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    $options[] = new FieldOptionGuess(
                        'advanced_filter_type',
                        'string',
                        FieldOptionGuess::HIGH_CONFIDENCE
                    );
                    break;
            }
        }

        // references one
        if (isset($metadata['referencesOne'][$fieldName])) {
            $options[] = new FieldOptionGuess(
                'template',
                'WhiteOctoberAdminBundle::fields/relation_one.html.twig',
                FieldOptionGuess::MEDIUM_CONFIDENCE
            );
        }

        // references many
        if (isset($metadata['referencesMany'][$fieldName])) {
            $options[] = new FieldOptionGuess(
                'template',
                'WhiteOctoberAdminBundle::fields/relation_many.html.twig',
                FieldOptionGuess::MEDIUM_CONFIDENCE
            );
        }

        return $options;
    }
}
