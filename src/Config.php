<?php

namespace Catcher;

use Catcher\Payloads\Payload;
use Catcher\Senders\Contracts\SenderContract;
use Catcher\Wrappers\ErrorWrapper;

class Config
{
    private array $config = [
        'accessToken',
        'transmitting',
        'sender',
        'endpoint',
        'contextCallback',
    ];
    private bool $transmitting = false;
    private string $accessToken;
    private SenderContract $sender;
    private string $endpoint;
    private \Closure|string|array|null $contextCallback = null;

    public function __construct(array $config = [])
    {
        $this->update($config);
    }

    public function getPayloadData($level, $initialPayload): array
    {
        $data = [];

        if(is_string($initialPayload)) {
            $data['title'] = $initialPayload;
        }

        if($initialPayload instanceof ErrorWrapper) {
            $data['code'] = $initialPayload->code;
            $data['title'] = $initialPayload->error;
            $data['line'] = $initialPayload->line;
            $data['file'] = $initialPayload->file;
            $data['error_type'] = $initialPayload->type;

            if($initialPayload->hasTrace()) {
                $data['stack'] = $initialPayload->trace;
            }
        }

        $data['uri'] = $this->endpoint;

        return array_merge_recursive(compact('level'), $data);
    }

    public function send(Payload $payload, string $accessToken): ?Response
    {
        return $this->transmitting()
            ? $this->sender->send($payload, $accessToken)
            : new Response(0, "Config is not transmitting.");
    }

    public function transmitting(): bool
    {
        return $this->transmitting;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function contextCallback(): \Closure|string|array|null
    {
        return $this->contextCallback;
    }

    protected function update(array $config): void
    {
        $config = array_merge([], Defaults::all()->toArray(), $config);

        foreach($config as $key => $value) {
            if(
                !in_array($key, $this->config) ||
                !property_exists($this, $key)
            ) {
                continue;
            }

            $this->{$key} = $value;
        }
    }
}
