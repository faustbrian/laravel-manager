<?php

declare(strict_types=1);

namespace Tests\Fixtures;

abstract class AbstractClass
{
    private string $name;

    private string $driver;

    public function __construct(string $name, string $driver)
    {
        $this->name = $name;
        $this->driver = $driver;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }
}
