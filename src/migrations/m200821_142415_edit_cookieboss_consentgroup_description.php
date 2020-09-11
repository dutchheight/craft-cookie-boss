<?php

namespace dutchheight\cookieboss\migrations;

use Craft;
use craft\db\Migration;

/**
 * m200821_142415_edit_cookieboss_consentgroup_description migration.
 */
class m200821_142415_edit_cookieboss_consentgroup_description extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('{{%cookieboss_consentgroup}}', 'desc', $this->text()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('{{%cookieboss_consentgroup}}', 'desc', $this->string(255)->notNull()->defaultValue(''));
    }
}
