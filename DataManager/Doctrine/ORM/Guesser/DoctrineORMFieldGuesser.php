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

        if (!$this->entityManager->getConfiguration()->getMetadataDriverImpl()->isTransient($class)) {
            $metadata = $this->entityManager->getClassMetadata($class);

            if ($metadata->hasAssociation($fieldName)) {
                /*$multiple = $metadata->isCollectionValuedAssociation($property);
                $mapping = $metadata->getAssociationMapping($property);

                return new TypeGuess(
                    'entity',
                    array(
                        'em' => $this->em,
                        'class' => $mapping['targetEntity'],
                        'multiple' => $multiple,
                    ),
                    Guess::HIGH_CONFIDENCE
                );*/
            } else {
                switch ($metadata->getTypeOfField($fieldName)) {
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
                    case 'datetime':
                    case 'vardatetime':
                    case 'datetimetz':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/date_time.html.twig',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'datetime',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
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
                    case 'decimal':
                    case 'float':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/float.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'number',
                            FieldOptionGuess::MEDIUM_CONFIDENCE
                        );
                        break;
                    case 'integer':
                    case 'bigint':
                    case 'smallint':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/integer.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'integer',
                            FieldOptionGuess::MEDIUM_CONFIDENCE
                        );
                        break;
                    case 'string':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/text.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'text',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'filterType',
                            'string',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        break;
                    case 'text':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/text.html.twig',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'textarea',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'filterType',
                            'string',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        break;
                    case 'time':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/time.html.twig',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'time',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        break;
                }
            }
        }

        return $options;
    }
}
