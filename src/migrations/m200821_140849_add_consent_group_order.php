<?php

namespace dutchheight\cookieboss\migrations;

use Craft;
use craft\db\Migration;

/**
 * m200821_140849_add_consent_group_order migration.
 */
class m200821_140849_add_consent_group_order extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->columnExists('{{%cookieboss_consentgroup}}', 'order')) {
            $this->addColumn('{{%cookieboss_consentgroup}}', 'order', $this->integer()->null()->after('id'));
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if ($this->db->columnExists('{{%cookieboss_consentgroup}}', 'order')) {
            $this->dropColumn('{{%cookieboss_consentgroup}}', 'order');
        }
    }
}
