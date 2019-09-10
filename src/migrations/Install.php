<?php
/**
 * Craft Cookie consent plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\craftcookieconsent\migrations;

use dutchheight\craftcookieconsent\CraftCookieConsent;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Craft Cookie consent Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Dutch Height
 * @package   CraftCookieConsent
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // craftcookieconsent_consenttype table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%craftcookieconsent_consenttype}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%craftcookieconsent_consenttype}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer(),
                    'handle' => $this->string(255)->notNull()->defaultValue(''),
                    'name' => $this->string(255)->notNull()->defaultValue(''),
                    'desc' => $this->string(255)->notNull()->defaultValue(''),
                    'enabled' => $this->boolean()->notNull()->defaultValue(1),
                    'required' => $this->boolean()->notNull()->defaultValue(0),
                    'defaultValue' => $this->boolean()->notNull()->defaultValue(0),
                ]
            );
        }

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // craftcookieconsent_consenttype table
        // $this->createIndex(
        //     $this->db->getIndexName(
        //         '{{%craftcookieconsent_consenttype}}',
        //         'some_field',
        //         true
        //     ),
        //     '{{%craftcookieconsent_consenttype}}',
        //     'some_field',
        //     true
        // );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    // craftcookieconsent_consenttype table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%craftcookieconsent_consenttype}}', 'siteId'),
            '{{%craftcookieconsent_consenttype}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
        $this->insert('{{%craftcookieconsent_consenttype}}', [
            'handle' => 'technical',
            'name' => 'Technical',
            'desc' => 'Needed to use the site',
            'required' => 1,
            'defaultValue' => 1,
        ], true);
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // craftcookieconsent_consenttype table
        $this->dropTableIfExists('{{%craftcookieconsent_consenttype}}');
    }
}
