<?php

declare(strict_types=1);

namespace Tests\Fixtures;

final class BarFactory
{
    public static function create(array $config): BarClass
    {
        return new BarClass($config['name'], $config['driver']);
    }
}
