<?php
namespace Synergitech;

class Postal
{
    public static function send($subject, $body, $to, $to_name = '', $from = null, $data = [], $bcc = [])
    {
        $theme = \Theme::instance();
        $client = new \Postal\Client(\Config::get('postal.url'), \Config::get('postal.api_key'));
        $message = new \Postal\SendMessage($client);

        if (is_array($bcc) && count($bcc) > 0) {
            if (\Fuel::$env == \Fuel::PRODUCTION) {
                $bccs = array();
                foreach ($bcc as $bcc_email => $bcc_name) {
                    if (is_array($bcc_name)) {
                        foreach ($bcc_name as $bcc_subemail => $bcc_subname) {
                            if (preg_match('/(.+?)(?:\+.+?)*@(.+?\..+)/', $bcc_subemail, $matches)) {
                                $bcc_subemail = $matches[1] . '@' . $matches[2];
                                if (isset($bccs[$bcc_subemail])) {
                                    continue;
                                }
                                $bccs[$bcc_subemail] = $bcc_subname;
                            }
                        }
                    } else {
                        if (isset($bccs[$bcc_email])) {
                            continue;
                        }
                        $bccs[$bcc_email] = $bcc_name;
                    }
                }
                foreach ($bccs as $email=>$name) {
                    $message->bcc($name . ' <' . $email . '>');
                }
            } else {
                $env = getenv('EMAIL');
                if (!$env) {
                    $env = 'root@localhost';
                }
                $message->bcc('Test User'.' <'.$env.'>');
            }
        }

        if (is_array($to)) {
            $uniq = [];
            foreach ($to as $ar_to_email => $ar_to_name) {
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
            }
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

        if ($from === null) {
            $from = \Config::get('postal.send-name');
        }
        $from_line = $from . ' <'.\Config::get('postal.send-address').'>';
        $message->from($from_line);

        $message->reply_to = \Config::get('postal.reply-to');
        $message->subject($subject);

        $data['subject'] = $subject;
        $data['body'] = $body;
        $html_view = $theme->view(\Config::get('postal.template_html'), $data, false)->render();
        $text_view = $theme->view(\Config::get('postal.template_text'), $data, false)->render();

        $message->plainBody($text_view);
        $message->htmlBody($html_view);

        $result = $message->send();

        foreach ($result->recipients() as $email => $message) {
            \Synergitech\Postal\Email::forge(array(
                'from_name' => $from,
                'from_email' => \Config::get('postal.send-address'),
                'subject' => $subject,
                'to_line' => $email,
                'body' => $body,
                'data' => json_encode($data),
                'postal_id' => $message->id(),
                'postal_token' => $message->token()
            ))->save();
        }

        return true;
    }
}
