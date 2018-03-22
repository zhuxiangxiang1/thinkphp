<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreatePostTable extends Migrator
{
    //文章表
    function up()
    {
        $table=$this->table("tp_post");
        $table->addColumn("post_title","string",['limit'=>256])
            ->addColumn('post_content',"text")
            ->addTimestamps('created_at','updated_at')
            ->setId("post_id")
            ->save();
    }

    function down()
    {
        $this->dropTable("tp_post");
    }
}
