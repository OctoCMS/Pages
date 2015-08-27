<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class PagesInstallMigration extends AbstractMigration
{
    public function up()
    {
        // Create tables:
        $this->createContentType();
        $this->createPage();
        $this->createPageVersion();

        // Add foreign keys:
        $table = $this->table('content_type');

        if (!$table->hasForeignKey('parent_id')) {
            $table->addForeignKey('parent_id', 'content_type', 'id', ['delete' => 'RESTRICT', 'update' => 'CASCADE']);
            $table->save();
        }

        $table = $this->table('page');

        if (!$table->hasForeignKey('current_version_id')) {
            $table->addForeignKey('current_version_id', 'page_version', 'id', ['delete' => 'RESTRICT', 'update' => 'CASCADE']);
            $table->save();
        }

        if (!$table->hasForeignKey('parent_id')) {
            $table->addForeignKey('parent_id', 'page', 'id', ['delete' => 'RESTRICT', 'update' => 'CASCADE']);
            $table->save();
        }

        if (!$table->hasForeignKey('content_type_id')) {
            $table->addForeignKey('content_type_id', 'content_type', 'id', ['delete' => 'RESTRICT', 'update' => 'CASCADE']);
            $table->save();
        }

        $table = $this->table('page_version');

        if (!$table->hasForeignKey('content_item_id')) {
            $table->addForeignKey('content_item_id', 'content_item', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
            $table->save();
        }

        if (!$table->hasForeignKey('page_id')) {
            $table->addForeignKey('page_id', 'page', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
            $table->save();
        }

        if (!$table->hasForeignKey('user_id')) {
            $table->addForeignKey('user_id', 'content_type', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
            $table->save();
        }

        if (!$table->hasForeignKey('image_id')) {
            $table->addForeignKey('image_id', 'file', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
            $table->save();
        }
    }

    protected function createContentType()
    {
        $table = $this->table('content_type', ['id' => false, 'primary_key' => ['id']]);

        if (!$this->hasTable('content_type')) {
            $table->addColumn('id', 'integer', ['signed' => false, 'null' => false]);
            $table->create();
        }

        if (!$table->hasColumn('name')) {
            $table->addColumn('name', 'string', ['limit' => 100, 'null' => false, 'default' => 'Content']);
        }

        if (!$table->hasColumn('parent_id')) {
            $table->addColumn('parent_id', 'integer', ['signed' => false, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('allowed_children')) {
            $table->addColumn('allowed_children', 'string', ['limit' => 1000, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('definition')) {
            $table->addColumn('definition', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => false]);
        }

        if (!$table->hasColumn('icon')) {
            $table->addColumn('icon', 'string', ['limit' => 30, 'null' => true, 'default' => null]);
        }

        $table->save();

        $table->changeColumn('name', 'string', ['limit' => 100, 'null' => false, 'default' => 'Content']);
        $table->changeColumn('parent_id', 'integer', ['signed' => false, 'null' => true, 'default' => null]);
        $table->changeColumn('allowed_children', 'string', ['limit' => 1000, 'null' => true, 'default' => null]);
        $table->changeColumn('definition', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => false]);
        $table->changeColumn('icon', 'string', ['limit' => 30, 'null' => true, 'default' => null]);

        $table->save();
    }

    protected function createPage()
    {
        $table = $this->table('page', ['id' => false, 'primary_key' => ['id']]);

        if (!$this->hasTable('page')) {
            $table->addColumn('id', 'char', ['limit' => 5, 'null' => false]);
            $table->create();
        }

        if (!$table->hasColumn('parent_id')) {
            $table->addColumn('parent_id', 'char', ['limit' => 5, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('current_version_id')) {
            $table->addColumn('current_version_id', 'integer', ['signed' => false, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('content_type_id')) {
            $table->addColumn('content_type_id', 'integer', ['signed' => false, 'null' => false, 'default' => 1]);
        }

        if (!$table->hasColumn('uri')) {
            $table->addColumn('uri', 'string', ['limit' => 500, 'null' => false]);
        }

        if (!$table->hasColumn('position')) {
            $table->addColumn('position', 'integer', ['signed' => false, 'null' => false, 'default' => 0]);
        }

        if (!$table->hasColumn('publish_date')) {
            $table->addColumn('publish_date', 'datetime', ['null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('expiry_date')) {
            $table->addColumn('expiry_date', 'datetime', ['null' => true, 'default' => null]);
        }

        if (!$table->hasIndex('uri')) {
            $table->addIndex('uri', ['unique' => false]);
        }

        $table->save();

        $table->changeColumn('parent_id', 'char', ['limit' => 5, 'null' => true, 'default' => null]);
        $table->changeColumn('current_version_id', 'integer', ['signed' => false, 'null' => true, 'default' => null]);
        $table->changeColumn('content_type_id', 'integer', ['signed' => false, 'null' => false, 'default' => 1]);
        $table->changeColumn('uri', 'string', ['limit' => 500, 'null' => false]);
        $table->changeColumn('position', 'integer', ['signed' => false, 'null' => false, 'default' => 0]);
        $table->changeColumn('publish_date', 'datetime', ['null' => true, 'default' => null]);
        $table->changeColumn('expiry_date', 'datetime', ['null' => true, 'default' => null]);

        $table->save();
    }

    protected function createPageVersion()
    {
        $table = $this->table('page_version', ['id' => false, 'primary_key' => ['id']]);

        if (!$this->hasTable('page_version')) {
            $table->addColumn('id', 'integer', ['signed' => false, 'null' => false]);
            $table->create();
        }

        if (!$table->hasColumn('page_id')) {
            $table->addColumn('page_id', 'char', ['limit' => 5, 'null' => false]);
        }

        if (!$table->hasColumn('version')) {
            $table->addColumn('version', 'integer', ['signed' => false, 'null' => false]);
        }

        if (!$table->hasColumn('title')) {
            $table->addColumn('title', 'string', ['limit' => 250, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('short_title')) {
            $table->addColumn('short_title', 'string', ['limit' => 50, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('description')) {
            $table->addColumn('description', 'string', ['limit' => 250, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('meta_description')) {
            $table->addColumn('meta_description', 'string', ['limit' => 250, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('content_item_id')) {
            $table->addColumn('content_item_id', 'char', ['limit' => 32, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('updated_date')) {
            $table->addColumn('updated_date', 'datetime', ['null' => false]);
        }

        if (!$table->hasColumn('template')) {
            $table->addColumn('template', 'string', ['limit' => 250, 'null' => false]);
        }

        if (!$table->hasColumn('image_id')) {
            $table->addColumn('image_id', 'char', ['limit' => 32, 'null' => true, 'default' => null]);
        }

        $table->save();

        $table->changeColumn('page_id', 'char', ['limit' => 5, 'null' => false]);
        $table->changeColumn('version', 'integer', ['signed' => false, 'null' => false]);
        $table->changeColumn('title', 'string', ['limit' => 250, 'null' => true, 'default' => null]);
        $table->changeColumn('short_title', 'string', ['limit' => 50, 'null' => true, 'default' => null]);
        $table->changeColumn('description', 'string', ['limit' => 250, 'null' => true, 'default' => null]);
        $table->changeColumn('meta_description', 'string', ['limit' => 250, 'null' => true, 'default' => null]);
        $table->changeColumn('content_item_id', 'char', ['limit' => 32, 'null' => true, 'default' => null]);
        $table->changeColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'default' => null]);
        $table->changeColumn('updated_date', 'datetime', ['null' => false]);
        $table->changeColumn('template', 'string', ['limit' => 250, 'null' => false]);
        $table->changeColumn('image_id', 'char', ['limit' => 32, 'null' => true, 'default' => null]);

        $table->save();
    }
}
