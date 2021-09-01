<?php

namespace AcMarche\Bottin\Tests\Behat;

use AcMarche\Bottin\Repository\FicheRepository;
use Behat\MinkExtension\Context\RawMinkContext;

class FeatureContext extends RawMinkContext
{
    private FicheRepository $ficheRepository;

    public function __construct(FicheRepository $ficheRepository)
    {
        $this->ficheRepository = $ficheRepository;
    }

    /**
     * @Given I am logged in as an admin
     */
    public function iAmLoggedInAsAnAdmin(): void
    {
        $this->visitPath('/login');
        // var_dump($this->getSession()->getPage()->getContent());
        $this->fillField('username', 'jf@marche.be');
        $this->fillField('password', 'homer');
        $this->pressButton('Me connecter');
    }

    /**
     * Given I am logged in as user :username.
     *
     * @Given /^I am logged in as user "([^"]*)"$/
     */
    public function iAmLoggedInAsUser(string $username): void
    {
        $this->visitPath('/login');
        $this->fillField('username', $username);
        $this->fillField('password', 'homer');
        $this->pressButton('Me connecter');
    }

    /**
     * @When /^I am login with user "([^"]*)" and password "([^"]*)"$/
     */
    public function iAmLoginWithUserAndPassword(string $email, string $password): void
    {
        $this->visitPath('/login');
        $this->fillField('username', $email);
        $this->fillField('password', $password);
        $this->pressButton('Me connecter');
    }

    /**
     * @Given /^I am on the page show fiche "([^"]*)"$/
     */
    public function iAmOnThePageShowEntry(string $name): void
    {
        $fiche = $this->ficheRepository->findOneBy(['societe' => $name]);
        $path = '/fiche/show/'.$fiche->getId();
        $this->visitPath($path);
    }

    /**
     * @return mixed[]|string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }

    private function fillField(string $field, string $value): void
    {
        $this->getSession()->getPage()->fillField($field, $value);
    }

    private function pressButton($button): void
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }
}
