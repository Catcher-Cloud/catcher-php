<?php

namespace Catcher\Wrappers;

class ErrorWrapper
{
    public function __construct(
        public int $code,
        public int $line,
        public string $file,
        public string $error,
        public ?string $type = null,
        public bool $isUncaught = false,
        public array $trace = []
    ) { }

    public function hasTrace(): bool
    {
        return !empty($this->trace);
    }
}
