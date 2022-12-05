<?php

namespace Catcher\Payloads;


use Catcher\Catcher;

class Payload
{
    private $data;
    private string $accessToken;

    function __construct($data, string $accessToken)
    {
        $this->data = $data;
        $this->accessToken = $accessToken;
    }

    public function getEndpointUri(): string
    {
        return rtrim($this->data['uri'], '/') . '/report';
    }

    protected function getLevel(): ?string
    {
        return array_key_exists('level', $this->data) ? $this->data['level'] : null;
    }

    protected function getCaller(): array
    {
        $result = [];

        foreach (debug_backtrace() as $value) {
            foreach (['function' => strtolower($this->getLevel()), 'class' => Catcher::class] as $k => $v) {
                if (!isset($value[$k]) || $value[$k] != $v) {
                    continue 2;
                }
            }

            $result[] = $value;
        }

        return !empty($result) ? [
            'file' => $result[0]['file'],
            'line' => $result[0]['line']
        ] : [];
    }

    public function __toString(): string
    {
        $caller = $this->getCaller();

        $data = [
            'title' => array_key_exists('title', $this->data) ? $this->data['title'] : null,
            'level' => array_key_exists('level', $this->data) ? $this->data['level'] : null,
            'error_type' => array_key_exists('error_type', $this->data) ? $this->data['error_type'] : 'Manual Trigger',
            'code' => array_key_exists('code', $this->data) ? $this->data['code'] : 0,
            'stack' => array_key_exists('stack', $this->data) ? $this->data['stack'] : debug_backtrace(),
            'request_uri' => array_key_exists('request_uri', $this->data) ? $this->data['request_uri'] : $_SERVER['REQUEST_URI'],
            'request_verb' => array_key_exists('request_verb', $this->data) ? $this->data['request_verb'] : $_SERVER['REQUEST_METHOD'],
        ];

        $data['file'] = array_key_exists('file', $this->data)
            ? $this->data['file']
            : $caller['file'];

        $data['line'] = array_key_exists('line', $this->data)
            ? $this->data['line']
            : $caller['line'];

        return json_encode($data);
    }
}
