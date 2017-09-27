<?php

namespace Synergitech\Postal;

class Webhook
{
    public static function ProcessWebhook()
    {
        $signature = \Input::headers('X-Postal-Signature', null);
        $body = file_get_contents('php://input');

        $json = \Input::json();
        if (isset($json['payload']['message'])) {
            $email = \Synergitech\Postal\Email::query()
                ->where('platform', 'postal')
                ->where('pm_id', $json['payload']['message']['id'])
                ->where('pm_token', $json['payload']['message']['token'])
                ->get_one();

            if ($email) {
                $webhook = \Synergitech\Postal\Email\Webhook::forge();
                $webhook->email_id = $email->id;
                $webhook->action = $json['event'];
                $webhook->payload = json_encode($json['payload']);
                $webhook->save();
            }

            $response = new \Response('', 200);
            $response->send(true);
        } else {
            \Log::warning("No Payload sent from Postal");
            $response = new \Response('No payload', 400);
            $response->send(true);
            exit;
        }
    }
}
