<?php

/*
 * This file is part of the WhiteOctoberAdminBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhiteOctober\AdminBundle\DataManager\Propel\Guesser;

use WhiteOctober\AdminBundle\Guesser\FieldGuesserInterface;
use WhiteOctober\AdminBundle\Guesser\FieldOptionGuess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * PropelFieldGuesser class
 *
 * @author William DURAND <william.durand1@gmail.com>
 */
class PropelFieldGuesser implements FieldGuesserInterface
{
    public function __construct()
    {
    }

    public function guessOptions($class, $fieldName)
    {
        $peerClass = $class.'Peer';
        $tableMap  = $peerClass::getTableMap();

        $options = array();

        $singularFieldName = preg_replace('#(.+)s$#', '$1', $fieldName);

        $fieldName = ucfirst($fieldName);
        
        if ($tableMap->hasRelation($fieldName) || $tableMap->hasRelation($singularFieldName)) {
            try {
                $relationMap = $tableMap->getRelation($fieldName);
            } catch(\PropelException $e) {
                $relationMap = $tableMap->getRelation($singularFieldName);
                $fieldName = $singularFieldName;
            }

            $multiple = $relationMap->getType() === \RelationMap::MANY_TO_MANY;
            $table = $multiple ? $relationMap->getLocalTable() : $relationMap->getForeignTable();

            $options[] = new FieldOptionGuess(
                'formType',
                'model',
                FieldOptionGuess::HIGH_CONFIDENCE
            );
            $options[] = new FieldOptionGuess(
                'formOptions',
                array(
                    'relation_map'  => $relationMap,
                    'class'         => $table->getClassname(),
                    'multiple'      => $multiple,
                ),
                FieldOptionGuess::HIGH_CONFIDENCE
            );
        } else if ($tableMap->hasColumnByPhpName($fieldName)) {
            switch (strtolower($tableMap->getColumnByPhpName($fieldName)->getType())) {
                case 'array':
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
                case 'decimal':
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
                case 'varchar':
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
