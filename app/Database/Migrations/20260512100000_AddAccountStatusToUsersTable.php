<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountStatusToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'account_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'approved',
                'after' => 'is_admin',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'account_status');
    }
}
