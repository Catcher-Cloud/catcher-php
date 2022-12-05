<?php

namespace Catcher\Senders\Contracts;

use Catcher\Payloads\Payload;
use Catcher\Response;

interface SenderContract
{
    function send(Payload $payload, string $accessToken): Response;
}
