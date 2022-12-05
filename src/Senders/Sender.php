<?php

namespace Catcher\Senders;

use Catcher\Payloads\Payload;
use Catcher\Response;
use Catcher\Senders\Contracts\SenderContract;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;

class Sender implements SenderContract
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    function send(Payload $payload, string $accessToken): Response
    {
        try {
            $request = new Request(
                method: 'POST',
                uri: $payload->getEndpointUri(),
                headers: [
                    'Auth-Token' => $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                body: $payload
            );

            $response = $this->client->sendRequest($request);
            $responseData = json_decode($response->getBody()->getContents());

            return new Response($response->getStatusCode(), $response->getReasonPhrase(), $responseData->id);
        } catch (ClientExceptionInterface $e) {
            dd($e);
        }
    }
}
