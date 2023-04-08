<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
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
    $manager = getManager();

    $manager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.connections')
        ->andReturn(['example' => ['driver' => 'error']]);

    expect($manager->getConnections())->toBe([]);

    $manager->connection('error');
})->throws(InvalidArgumentException::class, 'Connection [error] not configured.');

it('should change the default connection', function (): void {
    $manager = getManager();

    $manager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.default')
        ->andReturn('example');

    expect($manager->getDefaultConnection())->toBe('example');

    $manager->getConfig()
        ->shouldReceive('set')->once()
        ->with('manager.default', 'new');

    $manager->setDefaultConnection('new');

    $manager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.default')
        ->andReturn('new');

    expect($manager->getDefaultConnection())->toBe('new');
});

it('should register an extension with a callable function', function (): void {
    $manager = getManager();

    $manager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.connections')
        ->andReturn(['foo' => ['driver' => 'hello']]);

    $manager->extend('foo', fn (array $config) => new FooClass($config['name'], $config['driver']));

    expect($manager->getConnections())->toBe([]);

    $connection = $manager->connection('foo');

    expect($connection)->toBeInstanceOf(FooClass::class);
    expect($connection->getName())->toBe('foo');
    expect($connection->getDriver())->toBe('hello');
    expect($manager->getConnections())->toHaveKey('foo');
});

it('should register an extension with a callable array', function (): void {
    $manager = getManager();

    $manager->getConfig()
        ->shouldReceive('get')->once()
        ->with('manager.connections')
        ->andReturn(['qwerty' => ['driver' => 'bar']]);

    $manager->extend('bar', [BarFactory::class, 'create']);

    expect($manager->getConnections())->toBe([]);

    $connection = $manager->connection('qwerty');

    expect($connection)->toBeInstanceOf(BarClass::class);
    expect($connection->getName())->toBe('qwerty');
    expect($connection->getDriver())->toBe('bar');
    expect($manager->getConnections())->toHaveKey('qwerty');
});
