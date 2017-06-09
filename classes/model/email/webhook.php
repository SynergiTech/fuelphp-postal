<?php
namespace Synergitech\Postal\Email;

class Webhook extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'email_id',
        'action',
        'payload',
        'created_at',
        'updated_at',
    );

    protected static $_observers = array(
        'Orm\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => false,
        ),
        'Orm\Observer_UpdatedAt' => array(
            'events' => array('before_update'),
            'mysql_timestamp' => false,
        ),
    );

    protected static $_table_name = 'synergitech_email_webhooks';


    protected static $_belongs_to = array(
        'email'   => array(
            'key_from' => 'email_id',
            'model_to' => '\Synergitech\Postal\Email',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
}
