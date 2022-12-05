<?php

namespace Catcher;

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

//        $serialized = $payload->serialize($this->config->getMaxNestingDepth());

//        $scrubbed = $this->scrub($serialized);

//        $encoded = $this->encode($scrubbed);

//        $truncated = $this->truncate($encoded);

        return $this->send($payload, $accessToken);
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
