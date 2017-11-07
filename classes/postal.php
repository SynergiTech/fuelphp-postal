<?php
namespace Synergitech;

class Postal
{
    public static function send($subject, $body, $to, $to_name = '', $from = null, $data = [], $bcc = [])
    {
        $message = \Synergitech\Postal\SendMessage::forge($subject, $body, $data, $from);

        foreach ($bcc as $email => $name) {
            $message->bcc($email, $name);
        }

        if (is_array($to)) {
            foreach ($to as $email => $name) {
                $message->to($email, $name);
            }
        } else {
            $message->to($to, $to_name);
        }

        $message->send();

        return true;
    }
}
