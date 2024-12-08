<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Illuminate\Contracts\Config\Repository;
use Tests\Fixtures\ExampleManager;

function getManager(): ExampleManager
{
    return new ExampleManager(Mockery::mock(Repository::class));
}

function getConfigManager(array $config): ExampleManager
{
    $exampleManager = getManager();

    $exampleManager->getConfig()
        ->shouldReceive('get')->twice()
        ->with('manager.connections')
        ->andReturn(['example' => $config]);

    return $exampleManager;
}
