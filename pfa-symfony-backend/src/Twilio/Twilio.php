<?php
// src/Twilio/Twilio.php

namespace App\Twilio;

use Twilio\Exceptions\ConfigurationException;
use Twilio\Rest\Client;

class Twilio
{
    private Client $client;

    public function __construct(string $accountSid, string $authToken)
    {
        try {
            $this->client = new Client($accountSid, $authToken);
        } catch (ConfigurationException $e) {
            throw new \Exception('Twilio configuration error: ' . $e->getMessage());
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
