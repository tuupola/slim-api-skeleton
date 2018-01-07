<?php


use Phinx\Migration\AbstractMigration;

class Todo extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $todos = $this->table("todos");
        $todos
            ->addColumn("uid", "string", ["limit" => 16, "null" => false])
            ->addColumn("title", "string", ["limit" => 255, "null" => false])
            ->addColumn("completed", "integer", ["default" => 0])
            ->addColumn("order", "integer", ["default" => 512])
            ->addTimestamps()
            ->addIndex(["uid"], ["unique" => true, "name" => "uid_index"]);
        $todos->create();
    }
}
