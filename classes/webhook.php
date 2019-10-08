<?php
namespace Synergitech\Postal;

class Webhook
{
    public static function ProcessWebhook()
    {
        $json = \Input::json();
        if (isset($json['payload'])) {
            if (isset($json['payload']['message'])) {
                $postal_id = $json['payload']['message']['id'];
                $postal_token = $json['payload']['message']['token'];
            } elseif (isset($json['payload']['original_message'])) {
                $postal_id = $json['payload']['original_message']['id'];
                $postal_token = $json['payload']['original_message']['token'];
            }

            if (isset($postal_id) && isset($postal_token)) {
                if ($email = \Synergitech\Postal\Email::query()
                    ->where('postal_id', $postal_id)
                    ->where('postal_token', $postal_token)
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
                \Log::warning("Unable to process payload from Postal");
                $response = new \Response('Problem processing payload', 500);
            }
        } else {
            \Log::warning("No Payload sent from Postal");
            $response = new \Response('No payload', 400);
        }

        $response->send(true);
    }
}
