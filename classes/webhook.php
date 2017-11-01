<?php
namespace Synergitech\Postal;

class Webhook
{
    public static function ProcessWebhook()
    {
        $json = \Input::json();
        if (isset($json['payload']['message'])) {
            if ($email = \Synergitech\Postal\Email::query()
                ->where('postal_id', $json['payload']['message']['id'])
                ->where('postal_token', $json['payload']['message']['token'])
                ->get_one()
            ) {
                \Synergitech\Postal\Email\Webhook::forge(array(
                    'email_id' => $email->id,
                    'action' => $json['event'],
                    'payload' => json_encode($json['payload'])
                ))->save();
            }

            $response = new \Response('', 200);
        } else {
            \Log::warning("No Payload sent from Postal");
            $response = new \Response('No payload', 400);
        }
        $response->send(true);
    }
}
