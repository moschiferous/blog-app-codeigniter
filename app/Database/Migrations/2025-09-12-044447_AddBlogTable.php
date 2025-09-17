<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBlogTable extends Migration
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
            'title'=>[
                'type'=>'VARCHAR',
                'constraint'=>'255',
                'null'=>false,
            ],
            'slug'=>[
                'type'=>'VARCHAR',
                'constraint'=>'255',
                'null'=>false,
            ],
            'content'=>[
                'type'=>'TEXT',
                'null'=>false,
            ],
            'author_id'=>[
                'type'=>'INT',
                'constraint'=>5,
                'unsigned'=>true,
                'null'=>false,
            ],
            'image'=>[
                'type'=>'VARCHAR',
                'constraint'=>'255',
                'null'=>false,
            ],
            'created_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ],
            'updated_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ],
            'deleted_at'=>[
                'type'=>'DATETIME',
                'null'=>true,
            ]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('author_id','users','id');
        $this->forge->createTable('blogs');
    }

    public function down()
    {
        $this->forge->dropTable('blogs');
    }
}
