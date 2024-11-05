<?php

declare(strict_types=1);

use Illuminate\Contracts\Config\Repository;
use Tests\Fixtures\ExampleManager;

function getManager(): ExampleManager
{
    return new ExampleManager(Mockery::mock(Repository::class));
}

function getConfigManager(array $config): ExampleManager
{
    $manager = getManager();

    $manager->getConfig()
        ->shouldReceive('get')->twice()
        ->with('manager.connections')
        ->andReturn(['example' => $config]);

    return $manager;
}
