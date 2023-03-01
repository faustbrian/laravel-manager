<?php

declare(strict_types=1);

namespace PreemStudio\Manager\Contracts;

interface Connector
{
    public function connect(array $config): object;
}
