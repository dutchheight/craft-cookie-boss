<?php

namespace dutchheight\cookieboss\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\events\ConfigEvent;
use craft\helpers\Db;
use craft\helpers\StringHelper;

use dutchheight\cookieboss\records\CookieDescription;

class CookieDescriptionService extends Component {

    const CONFIG_KEY = 'plugins.cookie-boss.cookieDescriptions';
    const TABLE = '{{%cookieboss_cookiedescription}}';

    public function getAll($consentGroupHandle = null) {
        $cd = CookieDescription::find();
        if (!is_null($consentGroupHandle)) {
            $cg = CookieBoss::getInstance()->consentGroups->getAllByHandle($consentGroupHandle);
            $cd->where(['consentGroupId' => $cg->id]);
        }
        return $cd->orderBy('index')->all();
    }

    public function getAllEnabled() {
        return CookieDescription::find()->where(['enabled' => 1])->orderBy('index')->with('consentGroup')->all();
    }

    public function deleteAll() {
        return CookieDescription::deleteAll();
    }

    public function selectId($el) {
        if ($el['id']) {
            return (int)$el['id'];
        }
    }

    public function updateAll(Array $groups) {
        // All id's that are left over should be deleted
        $currentIds = array_map(
            'self::selectId',
            CookieDescription::find()->select(['id'])->asArray()->all()
        );
        $currentIndex = 0;

        foreach ($groups as $group) {
            // Check if cg is new
            if ($group['id']) {
                // Delete from array because id is still present
                if (($key = array_search($group['id'], $currentIds)) !== false) {
                    unset($currentIds[$key]);
                }

                // Check if cg has UUID
                $cd = CookieDescription::findOne(['id' => $group["id"]]);
                if (!$cd->uid) {
                    $cd->uid = Db::uidById(self::TABLE, $cd->id);
                }
            } else {
                // Make UUID for new Group
                $cd = new CookieDescription();
                $cd->uid = StringHelper::UUID();
            }

            $cd->enabled        = (!empty($group["enabled"]) ? $group["enabled"] : 0);
            $cd->index          = $currentIndex;
            $cd->consentGroupId = ($group["consentGroupId"] != "" && $group["consentGroupId"] != "-") ? $group["consentGroupId"] : null;
            $cd->name           = $group["name"];
            $cd->key            = $group["key"];
            $cd->purpose        = $group["purpose"];
            $cd->desc           = $group["desc"];

            // Save to config
            $this->saveCookieDescription($cd);
            $currentIndex ++;
        }

        if (count($currentIds) > 0) {
            $uids = Db::uidsByIds(self::TABLE, $currentIds);
            foreach ($uids as $uid) {
                Craft::$app->projectConfig->remove(self::CONFIG_KEY . "." .$uid);
            }
        }

        return true;
    }


    private function saveCookieDescription(CookieDescription $cookieDescription) {

        if (!$cookieDescription->validate()) {
            return false;
        }

        $values = [
            'enabled'           => $cookieDescription->enabled,
            'index'             => $cookieDescription->index,
            'consentGroupId'    => $cookieDescription->consentGroupId,
            'name'              => $cookieDescription->name,
            'key'               => $cookieDescription->key,
            'purpose'           => $cookieDescription->purpose,
            'desc'              => $cookieDescription->desc
        ];

        if (is_null($cookieDescription->consentGroupId)) {
            unset($values['consentGroupId']);
        }

        // Save it to the project config
        Craft::$app->projectConfig->set(self::CONFIG_KEY . "." .$cookieDescription->uid, $values);

        return true;
    }

    //
    // ─── Project config event handlers ───────────────────────────────────────────────────────────────────────────
    //

    public function handleChangedCookieDescription(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];

        // Does exist?
        $id = (new Query())
            ->select(['id'])
            ->from(self::TABLE)
            ->where(['uid' => $uid])
            ->scalar();

        $isNew = empty($id);
        if ($isNew) {
            Craft::$app->db->createCommand()->insert(self::TABLE, [
                    'uid'               => $uid,
                    'enabled'           => $event->newValue['enabled'],
                    'index'             => $event->newValue['index'],
                    'consentGroupId'    => (key_exists('consentGroupId', $event->newValue)) ? $event->newValue['consentGroupId'] : null,
                    'name'              => $event->newValue['name'],
                    'key'               => $event->newValue['key'],
                    'purpose'           => $event->newValue['purpose'],
                    'desc'              => $event->newValue['desc']
                ])->execute();
        } else {
            Craft::$app->db->createCommand()->update(self::TABLE, [
                    'uid'               => $uid,
                    'enabled'           => $event->newValue['enabled'],
                    'index'             => $event->newValue['index'],
                    'consentGroupId'    => (key_exists('consentGroupId', $event->newValue)) ? $event->newValue['consentGroupId'] : null,
                    'name'              => $event->newValue['name'],
                    'key'               => $event->newValue['key'],
                    'purpose'           => $event->newValue['purpose'],
                    'desc'              => $event->newValue['desc']
                ], ['id' => $id])->execute();
        }
    }

    public function handleDeleteCookieDescription(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        Craft::$app->db->createCommand()->delete(self::TABLE, ['uid' => $uid])->execute();
    }
}