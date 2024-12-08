<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Fixtures;

abstract class AbstractClass
{
    public function __construct(
        private readonly string $name,
        private readonly string $driver,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }
}
