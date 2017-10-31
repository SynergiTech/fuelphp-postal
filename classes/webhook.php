<?php
namespace Synergitech\Postal;

class Webhook
{
    public static function ProcessWebhook()
    {
        $json = \Input::json();
        if (isset($json['payload']['message'])) {
            $email = \Synergitech\Postal\Email::query()
                ->where('postal_id', $json['payload']['message']['id'])
                ->where('postal_token', $json['payload']['message']['token'])
                ->get_one();

            if ($email) {
                $webhook = \Synergitech\Postal\Email\Webhook::forge();
                $webhook->email_id = $email->id;
                $webhook->action = $json['event'];
                $webhook->payload = json_encode($json['payload']);
                $webhook->save();
            }

            $response = new \Response('', 200);
        } else {
            \Log::warning("No Payload sent from Postal");
            $response = new \Response('No payload', 400);
        }
        $response->send(true);
    }
}
