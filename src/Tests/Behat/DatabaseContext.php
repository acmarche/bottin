<?php

namespace AcMarche\Bottin\Tests\Behat;

use AcMarche\Bottin\Fixture\FixtureLoader;
use Behat\Behat\Context\Context;

class DatabaseContext implements Context
{
    public function __construct(private FixtureLoader $fixtureLoader)
    {
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
