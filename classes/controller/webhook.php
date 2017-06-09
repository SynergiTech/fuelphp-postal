<?php
namespace Synergitech\Postal;

class Controller_Webhook extends \Controller_Rest
{
    protected $auth = '';

    public function action_appmail()
    {
        \Synergitech\Postal\Webhook::ProcessWebhook();
    }
}
