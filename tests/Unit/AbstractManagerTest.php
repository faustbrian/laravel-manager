<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Unit;

use Tests\Fixtures\BarClass;
use Tests\Fixtures\BarFactory;
use Tests\Fixtures\ExampleClass;
use Tests\Fixtures\FooClass;

it('should have a connection with a name', function (): void {
    $manager = getConfigManager(['driver' => 'manager']);

    expect($manager->getConnections())->toBe([]);

    $connection = $manager->connection('example');

    expect($connection)->toBeInstanceOf(ExampleClass::class);
    expect($connection->getName())->toBe('example');
    expect($connection->getDriver())->toBe('manager');
    expect($manager->getConnections())->toHaveKey('example');

    $connection = $manager->reconnect('example');

    expect($connection)->toBeInstanceOf(ExampleClass::class);
    expect($connection->getName())->toBe('example');
    expect($connection->getDriver())->toBe('manager');
    expect($manager->getConnections())->toHaveKey('example');

    $manager = getManager();
    $manager->disconnect('example');

    expect($manager->getConnections())->toBe([]);
});

it('should return the default connection if the connection name is not provided', function (): void {
    $manager = getConfigManager(['driver' => 'manager']);

    $manager->getConfig()
        ->shouldReceive('get')->twice()
        ->with('manager.default')
        ->andReturn('example');

    expect($manager->getConnections())->toBe([]);

    $connection = $manager->connection();

    expect($connection)->toBeInstanceOf(ExampleClass::class);
    expect($connection->getName())->toBe('example');
    expect($connection->getDriver())->toBe('manager');
    expect($manager->getConnections())->toHaveKey('example');

    $connection = $manager->reconnect();

    expect($connection)->toBeInstanceOf(ExampleClass::class);
    expect($connection->getName())->toBe('example');
    expect($connection->getDriver())->toBe('manager');
    expect($manager->getConnections())->toHaveKey('example');

    $manager = getManager();

    $manager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.default')
        ->andReturn('example');

    $manager->disconnect();

    expect($manager->getConnections())->toBe([]);
});

it('should throw an exception if the connection is not configured', function (): void {
    $exampleManager = getManager();

    $exampleManager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.connections')
        ->andReturn(['example' => ['driver' => 'error']]);

    expect($exampleManager->getConnections())->toBe([]);

    $exampleManager->connection('error');
})->throws(\InvalidArgumentException::class, 'Connection [error] not configured.');

it('should change the default connection', function (): void {
    $exampleManager = getManager();

    $exampleManager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.default')
        ->andReturn('example');

    expect($exampleManager->getDefaultConnection())->toBe('example');

    $exampleManager->getConfig()
        ->shouldReceive('set')->once()
        ->with('manager.default', 'new');

    $exampleManager->setDefaultConnection('new');

    $exampleManager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.default')
        ->andReturn('new');

    expect($exampleManager->getDefaultConnection())->toBe('new');
});

it('should register an extension with a callable function', function (): void {
    $exampleManager = getManager();

    $exampleManager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.connections')
        ->andReturn(['foo' => ['driver' => 'hello']]);

    $exampleManager->extend('foo', fn (array $config): \Tests\Fixtures\FooClass => new FooClass($config['name'], $config['driver']));

    expect($exampleManager->getConnections())->toBe([]);

    $connection = $exampleManager->connection('foo');

    expect($connection)->toBeInstanceOf(FooClass::class);
    expect($connection->getName())->toBe('foo');
    expect($connection->getDriver())->toBe('hello');
    expect($exampleManager->getConnections())->toHaveKey('foo');
});

it('should register an extension with a callable array', function (): void {
    $exampleManager = getManager();

    $exampleManager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.connections')
        ->andReturn(['qwerty' => ['driver' => 'bar']]);

    $exampleManager->extend('bar', BarFactory::create(...));

    expect($exampleManager->getConnections())->toBe([]);

    $connection = $exampleManager->connection('qwerty');

    expect($connection)->toBeInstanceOf(BarClass::class);
    expect($connection->getName())->toBe('qwerty');
    expect($connection->getDriver())->toBe('bar');
    expect($exampleManager->getConnections())->toHaveKey('qwerty');
});
