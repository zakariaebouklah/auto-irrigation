<?php
namespace App\Service;

use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SmsService
{
    private string $twilioAccountSid;
    private string $twilioAuthToken;
    private string $twilioFromNumber;

    public function __construct(string $twilioAccountSid, string $twilioAuthToken, string $twilioFromNumber)
    {
        $this->twilioAccountSid = $twilioAccountSid;
        $this->twilioAuthToken = $twilioAuthToken;
        $this->twilioFromNumber = $twilioFromNumber;
    }

    public function sendSms(string $receiverPhone, string $message): string
    {
        $twilioClient = new Client($this->twilioAccountSid, $this->twilioAuthToken);
        try {
            $message = $twilioClient->messages->create(
                $receiverPhone,
                [
                    'from' => $this->twilioFromNumber,
                    'body' => $message,
                ]
            );
        } catch (TwilioException $e) {
            throw new \Exception('Error sending SMS: ' . $e->getMessage());
        }

        return $message->sid;
    }

}