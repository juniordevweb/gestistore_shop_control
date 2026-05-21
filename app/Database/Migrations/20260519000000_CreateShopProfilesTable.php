<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShopProfilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'shop_id' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => false,
            ],
            'shop_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('shop_id');
        $this->forge->addUniqueKey('shop_id');
        $this->forge->createTable('shop_profiles');
    }

    public function down()
    {
        $this->forge->dropTable('shop_profiles');
    }
}
