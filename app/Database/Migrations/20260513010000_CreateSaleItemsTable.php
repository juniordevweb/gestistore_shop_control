<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'sale_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'product_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true],
            'product_name'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'quantite'       => ['type' => 'INT', 'default' => 1],
            'prix_unitaire'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'sous_total'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'created_at'     => ['type' => 'TIMESTAMP', 'default' => 'CURRENT_TIMESTAMP'],
        ]);

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('sale_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sale_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('sale_items', true);
    }
}
