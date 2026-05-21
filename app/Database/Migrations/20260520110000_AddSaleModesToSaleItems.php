<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSaleModesToSaleItems extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('sale_items')) {
            return;
        }

        $fields = $this->db->getFieldData('sale_items');
        $fieldNames = array_map(static fn ($field) => $field->name, $fields);

        $columns = [];

        if (! in_array('mode_vente', $fieldNames, true)) {
            $columns['mode_vente'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'poids',
                'after' => 'unite_affichage',
            ];
        }

        if (! in_array('type_emballage', $fieldNames, true)) {
            $columns['type_emballage'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'sachet',
                'after' => isset($columns['mode_vente']) ? 'mode_vente' : 'unite_affichage',
            ];
        }

        if (! in_array('quantite_saisie', $fieldNames, true)) {
            $columns['quantite_saisie'] = [
                'type' => 'DECIMAL',
                'constraint' => '18,6',
                'default' => 0,
                'after' => isset($columns['type_emballage']) ? 'type_emballage' : (isset($columns['mode_vente']) ? 'mode_vente' : 'unite_affichage'),
            ];
        }

        if (! in_array('poids_emballage', $fieldNames, true)) {
            $columns['poids_emballage'] = [
                'type' => 'DECIMAL',
                'constraint' => '18,6',
                'default' => 0,
                'after' => isset($columns['quantite_saisie']) ? 'quantite_saisie' : 'unite_affichage',
            ];
        }

        if (! in_array('prix_emballage', $fieldNames, true)) {
            $columns['prix_emballage'] = [
                'type' => 'DECIMAL',
                'constraint' => '18,6',
                'default' => 0,
                'after' => isset($columns['poids_emballage']) ? 'poids_emballage' : 'unite_affichage',
            ];
        }

        if ($columns !== []) {
            $this->forge->addColumn('sale_items', $columns);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('sale_items')) {
            $this->forge->dropColumn('sale_items', [
                'mode_vente',
                'type_emballage',
                'quantite_saisie',
                'poids_emballage',
                'prix_emballage',
            ]);
        }
    }
}
