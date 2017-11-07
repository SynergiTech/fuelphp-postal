<?php
namespace Synergitech\Postal;

class SendMessage extends \Postal\SendMessage
{
    public static function forge($subject, $body, $data = array(), $from_name = null)
    {
        $message = new self(new \Postal\Client(self::getConfig('url'), self::getConfig('api_key')));

        $theme = \Theme::instance();
        if (isset($data['body_view']) && $body === null) {
            $body_text = $theme->view($data['body_view'], ['is_text' => true] + $data, false)->render();
            $body_html = $theme->view($data['body_view'], ['is_html' => true] + $data, false)->render();
        }

        if (! isset($body_text)) {
            $body_text = $body;
        }
        if (! isset($body_html)) {
            $body_html = $body;
        }

        $message->original_body = $body_text;
        $message->original_data = $data;

        if ($from_name === null) {
            $from_name = self::getConfig('send-name');
        }
        $message->from($from_name . ' <' . self::getConfig('send-address') . '>');

        $message->replyTo(self::getConfig('reply-to'));
        $message->subject($subject);

        $data['subject'] = $subject;
        $message->plainBody($theme->view(self::getConfig('template_text'), ['body' => $body_text] + $data, false)->render());
        $message->htmlBody($theme->view(self::getConfig('template_html'), ['body' => $body_html] + $data, false)->render());

        $justonereceipient = self::everythingToOneAddress();
        if ($justonereceipient !== false) {
            $message->to($justonereceipient);
        }

        return $message;
    }

    /**
     * Discover if everything should be emailed to one address
     */
    private static function everythingToOneAddress()
    {
        if (\Fuel::$env != \Fuel::PRODUCTION) {
            $env = getenv('EMAIL');
            if ($env !== false) {
                return $env;
            }

            $config = self::getConfig('sendallmessagesto', false);
            if ($config != '') {
                return $config;
            }
        }

        return false;
    }

    /**
     * Ensure all config options are set before use
     */
    private static function getConfig($item, $throwException = true)
    {
        $value = \Config::get("postal.$item", '');

        if ($value == '' && $throwException) {
            throw new \Exception("Config postal.$item not set or not accessible");
        }

        return $value;
    }

    /**
     * Check the presence of and reformat the To line from a programming-friendly way
     */
    private function handleAddressee($email, $name)
    {
        if (strlen($email) > 0) {
            // remove the plus component from the email address since Postal cannot handle it
            if (preg_match('/(.+?)(?:\+.+?)*@(.+?\..+)/', $email, $m)) {
                $email = $m[1] . '@' . $m[2];
            }

            if (strlen($name) > 0) {
                return $name . ' <' . $email . '>';
            }

            return $email;
        }

        throw new \Exception('email address required');
    }

    /**
     * Wrappers for all of the Postal wrapper functions
     */

    public function to($email = '', $name = '')
    {
        // slightly different check so you don't send an email to literally no one
        $justonereceipient = self::everythingToOneAddress();
        if ($justonereceipient === false || $justonereceipient == $email) {
            parent::to($this->handleAddressee($email, $name));
        }

        return $this;
    }

    public function cc($email = '', $name = '')
    {
        if (self::everythingToOneAddress() === false) {
            parent::cc($this->handleAddressee($email, $name));
        }

        return $this;
    }

    public function bcc($email = '', $name = '')
    {
        if (self::everythingToOneAddress() === false) {
            parent::bcc($this->handleAddressee($email, $name));
        }

        return $this;
    }

    public function send()
    {
        $result = parent::send();

        foreach ($result->recipients() as $email => $message) {
            \Synergitech\Postal\Email::forge(array(
                'from_name' => $this->attributes['from'],
                'from_email' => self::getConfig('send-address'),
                'subject' => $this->attributes['subject'],
                'to_line' => $email,
                'body' => $this->original_body,
                'data' => json_encode($this->original_data),
                'postal_id' => $message->id(),
                'postal_token' => $message->token()
            ))->save();
        }

        return true;
    }
}
