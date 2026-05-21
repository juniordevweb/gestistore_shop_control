<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsAdminToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'is_admin' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'default' => 0,
                'after' => 'shop_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'is_admin');
    }
}