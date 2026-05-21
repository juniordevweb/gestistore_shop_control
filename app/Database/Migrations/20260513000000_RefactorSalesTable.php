<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorSalesTable extends Migration
{
    public function up()
    {
        // 1. Créer une nouvelle table sales avec la structure refactorisée
        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'shop_id'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'client'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'total'          => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'cash'],
            'created_at'     => ['type' => 'DATETIME', 'null' => false],
        ]);

        $this->forge->addKey('id', false, true);
        $this->forge->addKey('shop_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('sales_new', true);

        // 2. Copier les données de l'ancienne table si elle existe
        if ($this->db->tableExists('sales')) {
            // Migrer les données avec le calcul du total
            $query = $this->db->query("
                INSERT INTO sales_new (shop_id, client, total, payment_method, created_at)
                SELECT shop_id, client, (prix * quantite), payment_method, created_at
                FROM sales
            ");
        }

        // 3. Supprimer l'ancienne table
        if ($this->db->tableExists('sales')) {
            $this->forge->dropTable('sales');
        }

        // 4. Renommer la nouvelle table
        $this->db->query('ALTER TABLE sales_new RENAME TO sales');
    }

    public function down()
    {
        // Restaurer structure si rollback
        $this->forge->dropTable('sales', true);
    }
}
