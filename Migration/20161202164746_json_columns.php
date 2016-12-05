<?php

use Phinx\Migration\AbstractMigration;

class JsonColumns extends AbstractMigration
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
        $this->execute("UPDATE content_type SET `allowed_children` = '{}' WHERE `allowed_children` = '' OR `allowed_children` IS NULL");
        $this->execute("UPDATE content_type SET `allowed_templates` = '{}' WHERE `allowed_templates` = '' OR `allowed_templates` IS NULL");
        $this->execute("UPDATE content_type SET `definition` = '{}' WHERE `definition` = '' OR `definition` IS NULL");

        $this->table('content_type')
            ->changeColumn('allowed_children', \Phinx\Db\Adapter\AdapterInterface::PHINX_TYPE_JSON, ['null' => true, 'default' => null])
            ->changeColumn('allowed_templates', \Phinx\Db\Adapter\AdapterInterface::PHINX_TYPE_JSON, ['null' => true, 'default' => null])
            ->changeColumn('definition', \Phinx\Db\Adapter\AdapterInterface::PHINX_TYPE_JSON, ['null' => true, 'default' => null])
            ->save();
    }
}
