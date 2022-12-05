<?php

namespace Catcher;

class Response
{
    public function __construct(
        private readonly int   $status,
        private readonly mixed $info,
        private readonly ?string $uuid = null
    ) { }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getInfo(): mixed
    {
        return $this->info;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function wasSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    public function getOccurrenceUrl(): ?string
    {
        if (is_null($this->uuid)) {
            return null;
        }

        if (!$this->wasSuccessful()) {
            return null;
        }

        return "https://app.catcher.cloud/issues/" . urlencode($this->uuid);
    }

    public function __toString()
    {
        $url = $this->getOccurrenceUrl();
        return "Status: $this->status\n" .
            "Body: " . json_encode($this->info) . "\n" .
            "URL: $url";
    }


}
