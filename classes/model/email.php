<?php
namespace Synergitech\Postal;

class Email extends \Orm\Model
{
    protected static $_properties = array(
        'id',
        'subject' => array('default' => null),
        'from_name' => array('default' => null),
        'from_email' => array('default' => null),
        'to_line' => array('default' => null),
        'body' => array('default' => null),
        'data' => array('default' => null),
        'postal_id' => array('default' => null),
        'postal_token' => array('default' => null),
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

    protected static $_table_name = 'synergitech_emails';

    protected static $_has_many = array(
        'webhooks'   => array(
            'key_from' => 'id',
            'model_to' => '\Synergitech\Postal\Email\Webhook',
            'key_to' => 'email_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
  );
}
