<?php declare(strict_types=1);

/**
 * Copyright (C) BaseCode Oy - All Rights Reserved
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace BaseCodeOy\Manager;

use BaseCodeOy\Manager\Contracts\Manager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;

abstract class AbstractManager implements Manager
{
    protected array $connections = [];

    protected array $extensions = [];

    public function __construct(
        protected Repository $configRepository,
    ) {
        //
    }

    public function __call(string $method, array $parameters)
    {
        return $this->connection()->{$method}(...$parameters);
    }

    #[\Override()]
    public function connection(?string $name = null): object
    {
        $name ??= $this->getDefaultConnection();

        $this->connections[$name] ??= $this->makeConnection($name);

        return $this->connections[$name];
    }

    #[\Override()]
    public function reconnect(?string $name = null): object
    {
        $name ??= $this->getDefaultConnection();

        $this->disconnect($name);

        return $this->connection($name);
    }

    #[\Override()]
    public function disconnect(?string $name = null): void
    {
        $name ??= $this->getDefaultConnection();

        unset($this->connections[$name]);
    }

    #[\Override()]
    public function getConnectionConfig(?string $name = null): array
    {
        $name ??= $this->getDefaultConnection();

        return $this->getNamedConfig('connections', 'Connection', $name);
    }

    #[\Override()]
    public function getDefaultConnection(): string
    {
        return $this->configRepository->get($this->getConfigName().'.default');
    }

    #[\Override()]
    public function setDefaultConnection(string $name): void
    {
        $this->configRepository->set($this->getConfigName().'.default', $name);
    }

    #[\Override()]
    public function extend(string $name, callable $resolver): void
    {
        if ($resolver instanceof \Closure) {
            $reflection = new \ReflectionFunction($resolver);

            if (!$reflection->isStatic()) {
                $resolver = $resolver->bindTo($this, $this);
            }
        }

        $this->extensions[$name] = $resolver;
    }

    #[\Override()]
    public function getConnections(): array
    {
        return $this->connections;
    }

    public function getConfig(): Repository
    {
        return $this->configRepository;
    }

    protected function makeConnection(string $name): object
    {
        $config = $this->getConnectionConfig($name);

        if (\array_key_exists($name, $this->extensions)) {
            return $this->extensions[$name]($config);
        }

        if (($driver = Arr::get($config, 'driver')) && \array_key_exists($driver, $this->extensions)) {
            return $this->extensions[$driver]($config);
        }

        return $this->createConnection($config);
    }

    protected function getNamedConfig(string $type, string $description, string $name): array
    {
        $data = $this->configRepository->get($this->getConfigName().'.'.$type);

        if (!\is_array($config = Arr::get($data, $name)) && !$config) {
            throw new \InvalidArgumentException(\sprintf('%s [%s] not configured.', $description, $name));
        }

        $config['name'] = $name;

        return $config;
    }

    abstract protected function createConnection(array $config): object;

    abstract protected function getConfigName(): string;
}
