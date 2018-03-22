<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateUserTable extends Migrator
{
    //用户表
    function up()
    {
        $table=$this->table("tp_user");
        $table->addColumn("user_name","string",["limit"=>32])
            ->addColumn("user_password","string",['limit'=>"64"])
            ->addTimestamps('created_at','updated_at')
            ->setId("user_id")
            ->save();
    }

    function down()
    {
        $this->dropTable("tp_user");
    }
}
