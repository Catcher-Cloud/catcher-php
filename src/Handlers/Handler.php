<?php

namespace Catcher\Handlers;

use Catcher\Logger;

abstract class Handler
{
    public function __construct(
        protected Logger $logger,
        protected $previousHandler = null,
        protected bool $registered = false
    ) {}

    public function handle(...$args)
    {
        if (!$this->registered()) {
            throw new \Exception(get_class($this) . ' has not been set up.');
        }
    }

    public function logger(): Logger
    {
        return $this->logger;
    }

    public function register()
    {
        $this->registered = true;
    }

    public function registered(): bool
    {
        return $this->registered;
    }
}
