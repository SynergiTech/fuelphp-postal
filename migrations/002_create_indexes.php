<?php

namespace Fuel\Migrations;

class Create_indexes
{
    public function up()
    {
        \DBUtil::create_index('synergitech_emails', array('postal_id', 'postal_token'), 'postal_id_token');
    }

    public function down()
    {
        \DBUtil::drop_index('synergitech_emails', 'postal_id_token');
    }
}
