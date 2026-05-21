<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBaseUnitsToProductsAndSaleItems extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('products')) {
            $fields = $this->db->getFieldData('products');
            $fieldNames = array_map(static fn ($field) => $field->name, $fields);

            if (!in_array('unite_base', $fieldNames, true)) {
                $this->forge->addColumn('products', [
                    'unite_base' => [
                        'type' => 'VARCHAR',
                        'constraint' => 10,
                        'default' => 'g',
                        'after' => 'quantite',
                    ],
                ]);
            }

            if (!in_array('unite_affichage', $fieldNames, true)) {
                $this->forge->addColumn('products', [
                    'unite_affichage' => [
                        'type' => 'VARCHAR',
                        'constraint' => 10,
                        'default' => 'g',
                        'after' => 'unite_base',
                    ],
                ]);
            }

            $this->forge->modifyColumn('products', [
                'prix_achat' => ['type' => 'DECIMAL', 'constraint' => '18,6', 'null' => false, 'default' => 0],
                'prix_vente' => ['type' => 'DECIMAL', 'constraint' => '18,6', 'null' => false, 'default' => 0],
                'quantite' => ['type' => 'DECIMAL', 'constraint' => '18,6', 'null' => false, 'default' => 0],
            ]);
        }

        if ($this->db->tableExists('sale_items')) {
            $fields = $this->db->getFieldData('sale_items');
            $fieldNames = array_map(static fn ($field) => $field->name, $fields);

            if (!in_array('cout_total', $fieldNames, true)) {
                $this->forge->addColumn('sale_items', [
                    'cout_total' => [
                        'type' => 'DECIMAL',
                        'constraint' => '18,6',
                        'default' => 0,
                        'after' => 'sous_total',
                    ],
                ]);
            }

            if (!in_array('benefice', $fieldNames, true)) {
                $this->forge->addColumn('sale_items', [
                    'benefice' => [
                        'type' => 'DECIMAL',
                        'constraint' => '18,6',
                        'default' => 0,
                        'after' => 'cout_total',
                    ],
                ]);
            }

            if (!in_array('unite_base', $fieldNames, true)) {
                $this->forge->addColumn('sale_items', [
                    'unite_base' => [
                        'type' => 'VARCHAR',
                        'constraint' => 10,
                        'default' => 'g',
                        'after' => 'benefice',
                    ],
                ]);
            }

            if (!in_array('unite_affichage', $fieldNames, true)) {
                $this->forge->addColumn('sale_items', [
                    'unite_affichage' => [
                        'type' => 'VARCHAR',
                        'constraint' => 10,
                        'default' => 'g',
                        'after' => 'unite_base',
                    ],
                ]);
            }

            $this->forge->modifyColumn('sale_items', [
                'quantite' => ['type' => 'DECIMAL', 'constraint' => '18,6', 'null' => false, 'default' => 0],
                'prix_unitaire' => ['type' => 'DECIMAL', 'constraint' => '18,6', 'null' => false, 'default' => 0],
                'sous_total' => ['type' => 'DECIMAL', 'constraint' => '18,6', 'null' => false, 'default' => 0],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('sale_items')) {
            $this->forge->dropColumn('sale_items', ['cout_total', 'benefice', 'unite_base', 'unite_affichage']);
        }

        if ($this->db->tableExists('products')) {
            $this->forge->dropColumn('products', ['unite_base', 'unite_affichage']);
        }
    }
}
