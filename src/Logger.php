<?php

namespace Catcher;

use Catcher\Payloads\Level;
use Catcher\Payloads\Payload;

class Logger
{
    public Config $config;
    public Builder $builder;

    function __construct(Config $config, ?Builder $builder = null) {
        $this->config = $config;
        $this->builder = !$builder ? new Builder : $builder;
    }

    public function log(string $level, $data = null): ?Response
    {
        $accessToken = $this->config->getAccessToken();

        $payload = $this->getPayload($accessToken, $level, $data);

        return $this->send($payload, $accessToken);
    }

    public function debug($data): ?Response
    {
        return self::log(Level::DEBUG, $data);
    }

    public function info($data): ?Response
    {
        return self::log(Level::INFO, $data);
    }

    public function warning($data): ?Response
    {
        return self::log(Level::WARNING, $data);
    }

    public function error($data): ?Response
    {
        return self::log(Level::ERROR, $data);
    }

    public function critical($data): ?Response
    {
        return self::log(Level::CRITICAL, $data);
    }

    public function builder(): Builder
    {
        return $this->builder;
    }

    protected function getPayload(string $accessToken, $level, $data)
    {
        $data = $this->config->getPayloadData($level, $data);

        if(!is_null($this->config->contextCallback())) {
            $context = $this->config->contextCallback();
            if(is_callable($context)) {
                $context = call_user_func($context);
            }
            $data = array_merge($data, $context);
        }

        return new Payload($data, $accessToken);
    }

    protected function send(Payload $payload, string $accessToken): ?Response
    {
        return $this->config->send($payload, $accessToken);
    }
}
