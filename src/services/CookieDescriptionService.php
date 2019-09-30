<?php

namespace dutchheight\cookieboss\services;

use Craft;
use craft\base\Component;
use dutchheight\cookieboss\records\CookieDescription;

class CookieDescriptionService extends Component {

    public static function getAll($consentGroupHandle = null) {
        $cd = CookieDescription::find();
        if (!is_null($consentGroupHandle)) {
            $cg = ConsentGroupService::getAllByHandle($consentGroupHandle);
            $cd->where(['consentGroupId' => $cg->id]);
        }
        return $cd->orderBy('index')->all();
    }

    public static function getAllEnabled() {
        return CookieDescription::find()->where(['enabled' => 1])->orderBy('index')->with('consentGroup')->all();
    }

    public static function deleteAll() {
        return CookieDescription::deleteAll();
    }

    public static function selectId($el) {
        if ($el['id']) {
            return (int)$el['id'];
        }
    }

    public static function updateAll(Array $groups) {
        // All id's that are left over should be deleted
        $currentIds = CookieDescription::find()->select(['id'])->asArray()->all();
        $currentIds = array_map('self::selectId', $currentIds);
        $currentIndex = 0;

        foreach ($groups as $group) {

            if ($group["name"] == "") {
                continue;
            }
            if ($group['id']) {
                // Delete from array because id is still present
                if (($key = array_search($group['id'], $currentIds)) !== false) {
                    unset($currentIds[$key]);
                }
                $cd = CookieDescription::findOne(['id' => $group["id"]]);
            } else {
                $cd = new CookieDescription();
            }

            $cd->enabled        = (!empty($group["enabled"]) ? $group["enabled"] : 0);
            $cd->index          = $currentIndex;
            $cd->consentGroupId = $group["consentGroupId"];
            $cd->name           = $group["name"];
            $cd->key            = $group["key"];
            $cd->purpose        = $group["purpose"];
            $cd->desc           = $group["desc"];

            if (!$cd->validate()) {
                throw new \Exception(json_encode($cd->errors));
            }
            $cd->save();
            $currentIndex ++;
        }

        if (count($currentIds) > 0) {
            $deleteIds = implode(', ', $currentIds);
            CookieDescription::deleteAll('id IN (' . $deleteIds . ')');
        }

        return true;
    }
}