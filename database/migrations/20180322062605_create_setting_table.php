<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateSettingTable extends Migrator
{
    function up()
    {
        //设置表
        $table=$this->table("tp_setting");
        $table->addColumn("setting_name","string",["limit"=>256])
            ->addColumn("setting_value","string",["limit"=>256])
            ->setId("setting_id")
            ->save();
    }

    function down()
    {
        $this->dropTable("tp_setting");
    }
}
