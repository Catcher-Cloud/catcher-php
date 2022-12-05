<?php

namespace Catcher;

use Catcher\Contracts\Arrayable;
use Catcher\Senders\Contracts\SenderContract;
use Catcher\Senders\Sender;
use GuzzleHttp\Client;
use Throwable;
use Traversable;

class Defaults implements \ArrayAccess, \IteratorAggregate, Arrayable
{
    private static $singleton = null;
    private bool $transmitting = true;
    private ?string $endpoint = 'https://api.catcher.cloud';
    private ?string $platform;
    private ?string $baseException;
    private SenderContract $sender;

    function __construct()
    {
        $this->platform = php_uname('a');
        $this->baseException = Throwable::class;
        $this->sender = new Sender(new Client);
    }

    public static function all(): ?Defaults
    {
        if (is_null(self::$singleton)) {
            self::$singleton = new static;
        }

        return self::$singleton;
    }

    public function endpoint(?string $endpoint = null): string
    {
        return !$endpoint ? $this->endpoint : $endpoint;
    }

    public function platform(?string $platform = null): string
    {
        return !$platform ? $this->platform : $platform;
    }

    public function baseException(?string $baseException = null): ?string
    {
        return !$baseException ? $this->baseException : $baseException;
    }

    public function transmitting(?bool $transmitting = null): bool
    {
        return $transmitting === null ? $this->transmitting : $transmitting;
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->{$offset} = null;
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this);
    }

    /**
     * @inheritDoc
     */
    function toArray(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'platform' => $this->platform,
            'baseException' => $this->baseException,
            'transmitting' => $this->transmitting,
            'sender' => $this->sender,
        ];
    }
}
