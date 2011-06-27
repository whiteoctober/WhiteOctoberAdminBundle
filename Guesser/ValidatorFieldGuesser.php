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

use Symfony\Component\Validator\Mapping\ClassMetadataFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\Constraint;

class ValidatorFieldGuesser implements FieldGuesserInterface
{
    private $metadataFactory;

    public function __construct(ClassMetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    public function guessOptions($class, $fieldName)
    {
        $options = array();

        $classMetadata = $this->metadataFactory->getClassMetadata($class);
        // normal and camelized for getters
        foreach (array($fieldName, Container::camelize($fieldName)) as $name) {
            if ($classMetadata->hasMemberMetadatas($name)) {
                foreach ($classMetadata->getMemberMetadatas($name) as $memberMetadata) {
                    foreach ($memberMetadata->getConstraints() as $constraint) {
                        $options = array_merge($options, $this->guessOptionsForConstraint($constraint));
                    }
                }
            }
        }

        return $options;
    }

    private function guessOptionsForConstraint(Constraint $constraint)
    {
        $options = array();

        switch (get_class($constraint)) {
            case 'Symfony\Component\Validator\Constraints\Type':
                switch ($constraint->type) {
                    case 'boolean':
                    case 'bool':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/boolean.html.twig',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'checkbox',
                            FieldOptionGuess::MEDIUM_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'filterType',
                            'boolean',
                            FieldOptionGuess::HIGH_CONFIDENCE
                        );
                        break;
                    case 'double':
                    case 'float':
                    case 'numeric':
                    case 'real':
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
                    case 'int':
                    case 'long':
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
                    case '\DateTime':
                        $options[] = new FieldOptionGuess(
                            'template',
                            'WhiteOctoberAdminBundle::fields/date.html.twig',
                            FieldOptionGuess::MEDIUM_CONFIDENCE
                        );
                        $options[] = new FieldOptionGuess(
                            'formType',
                            'date',
                            FieldOptionGuess::LOW_CONFIDENCE
                        );
                        break;
                }
                break;
            case 'Symfony\Component\Validator\Constraints\Choice':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'choice',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                $options[] = new FieldOptionGuess(
                    'formOptions',
                    array('choices' => $constraint->choices),
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Country':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'country',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Date':
                $options[] = new FieldOptionGuess(
                    'template',
                    'WhiteOctoberAdminBundle::fields/date.html.twig',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                $options[] = new FieldOptionGuess(
                    'formOptions',
                    'date',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\DateTime':
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
                break;
            case 'Symfony\Component\Validator\Constraints\Email':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'text',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\File':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'file',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Image':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'file',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Ip':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'text',
                    FieldOptionGuess::MEDIUM_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Language':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'language',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Locale':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'locale',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Max':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'number',
                    FieldOptionGuess::LOW_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\MaxLength':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'text',
                    FieldOptionGuess::LOW_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Min':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'number',
                    FieldOptionGuess::LOW_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\MinLength':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'text',
                    FieldOptionGuess::LOW_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\Regex':
                break;
            case 'Symfony\Component\Validator\Constraints\Time':
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
            case 'Symfony\Component\Validator\Constraints\Url':
                $options[] = new FieldOptionGuess(
                    'formType',
                    'url',
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
        }

        switch (get_class($constraint)) {
            case 'Symfony\Component\Validator\Constraints\NotNull':
                $options[] = new FieldOptionGuess(
                    'formOptions',
                    array('required' => true),
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            case 'Symfony\Component\Validator\Constraints\NotBlank':
                $options[] = new FieldOptionGuess(
                    'formOptions',
                    array('required' => true),
                    FieldOptionGuess::HIGH_CONFIDENCE
                );
                break;
            default:
                $options[] = new FieldOptionGuess(
                    'formOptions',
                    array('required' => false),
                    FieldOptionGuess::LOW_CONFIDENCE
                );
                break;
        }

        return $options;
    }
}
