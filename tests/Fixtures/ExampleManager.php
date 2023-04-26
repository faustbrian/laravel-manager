<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use BombenProdukt\Manager\AbstractManager;

final class ExampleManager extends AbstractManager
{
    protected function createConnection(array $config): ExampleClass
    {
        return new ExampleClass($config['name'], $config['driver']);
    }

    protected function getConfigName(): string
    {
        return 'manager';
    }
}
