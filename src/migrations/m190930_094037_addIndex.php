<?php

namespace dutchheight\cookieboss\migrations;

use Craft;
use craft\db\Migration;

/**
 * m190930_094037_addIndex migration.
 */
class m190930_094037_addIndex extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createColumns();
    }

    public function createColumns()
    {
        if (!$this->db->columnExists('{{%cookieboss_cookiedescription}}', 'index')) {
            $this->addColumn('{{%cookieboss_cookiedescription}}', 'index', $this->integer()->null()->after('uid'));
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190930_094037_addIndex cannot be reverted.\n";
        return false;
    }
}
