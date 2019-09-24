<?php
/**
 * Craft Cookie boss plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

namespace dutchheight\cookieboss\records;

use dutchheight\cookieboss\services\ConsentService;

use Craft;
use craft\db\ActiveRecord;

/**
 * ConsentGroup Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Dutch Height
 * @package   CookieBoss
 * @since     1.0.0
 */
class ConsentGroup extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    public function init() {
        parent::init();

        $this->enabled = 1;
        $this->required = 0;
        $this->defaultValue = 1;
    }

     /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by plugins should be prefixed with the plugin
     * name and an underscore.
     *
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%cookieboss_consentgroup}}';
    }

    public function rules()
    {
        return [
            [['handle', 'name', 'desc'], 'required']
        ];
    }

    public function hasConsent() {
        return ConsentService::isConsentWith($this->handle);
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true) {
        $parent = parent::toArray();
        $parent['hasConsent'] = $this->hasConsent();
        $parent['hasAnyConsent'] = ConsentService::hasConsentCookie();
        return $parent;
    }
}
