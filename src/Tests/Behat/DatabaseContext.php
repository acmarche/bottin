<?php

namespace AcMarche\Bottin\Tests\Behat;

use AcMarche\Bottin\Fixture\FixtureLoader;
use Behat\Behat\Context\Context;

class DatabaseContext implements Context
{
    /**
     * @var FixtureLoader
     */
    private $fixtureLoader;

    public function __construct(FixtureLoader $fixtureLoader)
    {
        $this->fixtureLoader = $fixtureLoader;
    }

    /**
     * @BeforeScenario
     */
    public function loadFixtures(): void
    {
        $this->fixtureLoader->load();
    }

    /**
     * @AfterScenario
     */
    public function rollbackPostgreSqlTransaction(): void
    {
    }
}