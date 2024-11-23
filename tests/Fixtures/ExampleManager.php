<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

use BaseCodeOy\Manager\AbstractManager;

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
