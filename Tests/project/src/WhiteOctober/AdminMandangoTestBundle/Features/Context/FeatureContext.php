<?php

namespace WhiteOctober\AdminMandangoTestBundle\Features\Context;

use Behat\BehatBundle\Context\BehatContext,
    Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends MinkContext //MinkContext if you want to test web
{
    /**
     * @Given /^The database is empty$/
     */
    public function theDatabaseIsEmpty()
    {
        foreach ($this->getMandango()->getAllRepositories() as $repository) {
            $repository->getCollection()->drop();
        }
    }

    /**
     * @Given /^I have an author "([^"]*)"$/
     */
    public function iHaveAnAuthor($name)
    {
        $this->getMandango()->create('Model\WhiteOctoberAdminMandangoTestBundle\Author')
            ->setName($name)
            ->save()
        ;
    }

    /**
     * @Given /^I have a category "([^"]*)"$/
     */
    public function iHaveACategory($name)
    {
        $this->getMandango()->create('Model\WhiteOctoberAdminMandangoTestBundle\Category')
            ->setName($name)
            ->save()
        ;
    }


    private function getMandango()
    {
        return $this->getContainer()->get('mandango');
    }
}
