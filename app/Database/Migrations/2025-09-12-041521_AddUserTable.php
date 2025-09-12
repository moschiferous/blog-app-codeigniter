<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'=>[
                'type'=>'INT',
                'constraint'=>5,
                'unsigned'=>true,
                'auto_increment'=>true
            ],
            'name'=>[
                'type'=>'VARCHAR',
                'constraint'=>'100',
                'null'=>false,
            ],
            'email'=>[
                'type'=>'VARCHAR',
                'constraint'=>'150',
                'null'=>false,
            ],
            'password'=>[
                'type'=>'VARCHAR',
                'constraint'=>'255',
                'null'=>false,
            ],
            'token'=>[
                'type'=>'TEXT',
                'null'=>false,
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
