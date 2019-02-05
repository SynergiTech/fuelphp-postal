<?php

namespace Fuel\Migrations;

class Update_datatype
{
    public function up()
    {
        \DBUtil::modify_fields('synergitech_emails', array(
            'postal_id' => array('type' => 'int'),
        ));
    }

    public function down()
    {
        \DBUtil::modify_fields('synergitech_emails', array(
            'postal_id' => array('constraint' => 255, 'type' => 'varchar'),
        ));
    }
}
