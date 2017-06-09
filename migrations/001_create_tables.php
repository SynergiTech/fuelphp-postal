<?php

namespace Fuel\Migrations;

class Create_tables
{
    public function up()
    {
        \DBUtil::create_table('synergitech_emails', array(
            'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
            'subject' => array('constraint' => 255, 'type' => 'varchar'),
            'from_name' => array('constraint' => 255, 'type' => 'varchar'),
            'from_email' => array('constraint' => 255, 'type' => 'varchar'),
            'to_line' => array('constraint' => 255, 'type' => 'varchar'),
            'body' => array('type' => 'text'),
            'data' => array('type' => 'text'),
            'postal_id' => array('constraint' => 255, 'type' => 'varchar'),
            'postal_token' => array('constraint' => 255, 'type' => 'varchar'),
            'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
        ), array('id'));

        \DBUtil::create_table('synergitech_email_webhooks', array(
            'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true, 'unsigned' => true),
            'email_id' => array('constraint' => 30, 'type' => 'int'),
            'action' => array('constraint' => 100, 'type' => 'varchar'),
            'payload' => array('type' => 'text'),
            'created_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
            'updated_at' => array('constraint' => 11, 'type' => 'int', 'null' => true),
        ), array('id'));
    }

    public function down()
    {
        \DBUtil::drop_table('synergitech_email_webhooks');
        \DBUtil::drop_table('synergitech_emails');
    }
}
