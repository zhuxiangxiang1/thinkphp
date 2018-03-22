<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateCommentTable extends Migrator
{
    function up()
    {
        //评论表
        $table=$this->table("tp_comment");
        $table->addColumn("post_id","integer")
            ->addColumn("comment_content","text")
            ->addColumn("created","timestamp")
            ->setId("comment_id")
            ->save();
    }

    function down()
    {
        $this->dropTable("tp_comment");
    }
}
