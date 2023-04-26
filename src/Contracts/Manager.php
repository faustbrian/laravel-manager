<?php

declare(strict_types=1);

namespace BombenProdukt\Manager\Contracts;

interface Manager
{
    public function connection(?string $name = null): object;

    public function reconnect(?string $name = null): object;

    public function disconnect(?string $name = null): void;

    public function getConnectionConfig(?string $name = null): array;

    public function getDefaultConnection(): string;

    public function setDefaultConnection(string $name): void;

    public function extend(string $name, callable $resolver): void;

    public function getConnections(): array;
}
