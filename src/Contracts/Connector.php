<?php

declare(strict_types=1);

namespace BombenProdukt\Manager\Contracts;

interface Connector
{
    public function connect(array $config): object;
}
