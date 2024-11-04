<?php

declare(strict_types=1);

namespace BaseCodeOy\Manager\Contracts;

interface Connector
{
    public function connect(array $config): object;
}
