<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class ContentTypeTemplates extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $table = $this->table('content_type');

        if (!$table->hasColumn('allowed_templates')) {
            $table->addColumn('allowed_templates', 'text', ['limit' => MysqlAdapter::TEXT_REGULAR])->save();
        }
    }
}
