<?php

namespace dutchheight\cookieboss\services;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\events\ConfigEvent;
use craft\helpers\Db;
use craft\helpers\StringHelper;

use dutchheight\cookieboss\records\ConsentGroup;

class ConsentGroupService extends Component {

    const CONFIG_KEY = 'plugins.cookie-boss.consentGroups';
    const TABLE = '{{%cookieboss_consentgroup}}';

    public function getAll() {
        return ConsentGroup::find()->orderBy('id')->all();
    }

    public function getAllByHandle($handle) {
        return ConsentGroup::find()->where(['handle' => $handle])->one();
    }

    public function getAllAsSelectOptions() {
        $returner[] = [
            'value' => null,
            'label' => '-'
        ];
        foreach ($this->getAll() as $value) {
            $returner[] = [
                'value' => $value['id'],
                'label' => $value['name']
            ];
        }

        return $returner;
    }

    public function getAllEnabled() {
        return ConsentGroup::find()->where(['enabled' => 1])->orderBy('id')->all();
    }

    public function deleteAll() {
        return ConsentGroup::deleteAll();
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
            ConsentGroup::find()->select(['id'])->asArray()->all()
        );

        foreach ($groups as $group) {
            // Check if cg is new
            if ($group['id']) {
                // Delete from array because id is still present
                if (($key = array_search($group['id'], $currentIds)) !== false) {
                    unset($currentIds[$key]);
                }

                // Check if cg has UUID
                $cg = ConsentGroup::findOne(['id' => $group["id"]]);
                if (!$cg->uid) {
                    $cg->uid = Db::uidById(self::TABLE, $cg->id);
                }
            } else {
                // Make UUID for new Group
                $cg = new ConsentGroup();
                $cg->uid = StringHelper::UUID();
            }

            // Update props
            $cg->enabled      = (!empty($group["enabled"]) ? $group["enabled"] : 0);
            $cg->defaultValue = (!empty($group["defaultValue"]) ? $group["defaultValue"] : 0);
            $cg->required     = (!empty($group["required"]) ? $group["required"] : 0);
            $cg->handle       = $group["handle"];
            $cg->name         = $group["name"];
            $cg->desc         = $group["desc"];

            // Save to config
            $this->saveConsentGroup($cg);
        }

        if (count($currentIds) > 0) {
            $uids = Db::uidsByIds(self::TABLE, $currentIds);
            foreach ($uids as $uid) {
                Craft::$app->projectConfig->remove(self::CONFIG_KEY . "." .$uid);
            }
        }

        return true;
    }

    private function saveConsentGroup(ConsentGroup $consentGroup) {

        if (!$consentGroup->validate()) {
            return false;
        }

        // Save it to the project config
        Craft::$app->projectConfig->set(self::CONFIG_KEY . "." .$consentGroup->uid, [
            'enabled'      => $consentGroup->enabled,
            'defaultValue' => $consentGroup->defaultValue,
            'required'     => $consentGroup->required,
            'handle'       => $consentGroup->handle,
            'name'         => $consentGroup->name,
            'desc'         => $consentGroup->desc
        ]);

        return true;
    }

    //
    // ─── Project config event handlers ───────────────────────────────────────────────────────────────────────────
    //

    public function handleChangedConsentGroup(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];

        $id = (new Query())
            ->select(['id'])
            ->from(self::TABLE)
            ->where(['uid' => $uid])
            ->scalar();

        $isNew = empty($id);
        if ($isNew) {
            Craft::$app->db->createCommand()->insert(self::TABLE, [
                    'uid'          => $uid,
                    'enabled'      => $event->newValue['enabled'],
                    'defaultValue' => $event->newValue['defaultValue'],
                    'required'     => $event->newValue['required'],
                    'handle'       => $event->newValue['handle'],
                    'name'         => $event->newValue['name'],
                    'desc'         => $event->newValue['desc']
                ])->execute();
        } else {
            Craft::$app->db->createCommand()->update(self::TABLE, [
                    'uid'          => $uid,
                    'enabled'      => $event->newValue['enabled'],
                    'defaultValue' => $event->newValue['defaultValue'],
                    'required'     => $event->newValue['required'],
                    'handle'       => $event->newValue['handle'],
                    'name'         => $event->newValue['name'],
                    'desc'         => $event->newValue['desc']
                ], ['id' => $id])->execute();
        }
    }

    public function handleDeleteConsentGroup(ConfigEvent $event)
    {
        $uid = $event->tokenMatches[0];
        Craft::$app->db->createCommand()->delete(self::TABLE, ['uid' => $uid])->execute();
    }
}