<?php

namespace Synergitech;

class Postal
{
    public static function send($subject, $body, $to, $to_name = '', $from = null, $data = array())
    {
        $theme = \Theme::instance();
        // Create a new Postal client using the server key you generate in our web interface
        $client = new \Postal\Client(\Config::get('postal.url'), \Config::get('postal.api_key'));
        // Create a new message
        $message = new \Postal\SendMessage($client);

        if (is_array($to)) {
            $uniq = [];
            foreach ($to as $ar_to_email => $ar_to_name):
                // send grid rejects josh+a josh+b as they are the same
                if (preg_match('/(.+?)(?:\+.+?)*@(.+?\..+)/', $ar_to_email, $m)) {
                    $email = $m[1].'@'.$m[2];
                    if (isset($uniq[$email])) {
                        continue;
                    }
                    $uniq[$email] = true;
                }

            if (\Fuel::$env != \Fuel::DEVELOPMENT) {
                $message->to($ar_to_name.' <'.$ar_to_email.'>');
            } else {
                $env = getenv('email');
                if (!$env) {
                    $env = 'root@localhost';
                }
                $message->to($ar_to_name.' <'.$env.'>');
                break;
            }
            endforeach;
        } else {
            if (\Fuel::$env != \Fuel::DEVELOPMENT) {
                $message->to($to_name.' <'.$to.'>');
            } else {
                $env = getenv('EMAIL');
                if (!$env) {
                    $env = 'root@localhost';
                }
                $message->to($to_name.' <'.$env.'>');
            }
        }

        // Specify who the message should be from. This must be from a verified domain
        // on your mail server.
        if ($from == null) {
            $from = \Config::get('postal.send-name');
        }
        $from_line = $from.' <'.\Config::get('postal.send-address').'>';
        $message->from($from_line);

        $message->reply_to = \Config::get('postal.reply-to');
        // Set the subejct
        $message->subject($subject);

        // Set the content for the e-mail
        $data['subject'] = $subject;
        $data['body'] = $body;
        $html_view = $theme->view(\Config::get('postal.template_html'), $data, false)->render();
        $text_view = $theme->view(\Config::get('postal.template_text'), $data, false)->render();

        $message->plainBody($text_view);
        $message->htmlBody($html_view);

        // Send the message and get the result

        $result = $message->send();
        // Loop through each of the recipients to get the message ID

        foreach ($result->recipients() as $email => $message) {
            $logEmail = \Synergitech\Postal\Email::forge();
            $logEmail->from_name = $from;
            $logEmail->from_email = \Config::get('postal.send-address');
            $logEmail->subject = $subject;
            $logEmail->to_line = $email;
            $logEmail->body = $body;
            $logEmail->data = json_encode($data);
            $logEmail->postal_id = $message->id();
            $logEmail->postal_token = $message->token();
            $logEmail->save();
            unset($logEmail);
        }

        return true;
    }
}