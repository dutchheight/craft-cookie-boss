<?php

namespace dutchheight\craftcookieconsent\services;

use Craft;
use craft\base\Component;
use dutchheight\craftcookieconsent\records\ConsentGroup;

class ConsentGroupService extends Component {
    
    public static function getAll() {
        return ConsentGroup::find()->orderBy('id')->all();
    }

    public static function getAllByHandle($handle) {
        return ConsentGroup::find()->where(['handle' => $handle])->one();
    }

    public static function getAllAsSelectOptions() {
        $returner[] = [
            'value' => null,
            'label' => '-'
        ];
        foreach (ConsentGroupService::getAll() as $key => $value) {
            $returner[] = [
                'value' => $value['id'],
                'label' => $value['name']
            ];
        }

        return $returner;
    }
    
    public static function getAllEnabled() {
        return ConsentGroup::find()->where(['enabled' => 1])->orderBy('id')->all();
    }

    public static function deleteAll() {
        return ConsentGroup::deleteAll();
    }

    public static function selectId($el) {
        if ($el['id']) {
            return (int)$el['id'];
        }
    }

    public static function updateAll(Array $groups) {
        
        // All id's that are left over should be deleted
        $currentIds = ConsentGroup::find()->select(['id'])->asArray()->all();
        $currentIds = array_map('self::selectId', $currentIds);

        foreach ($groups as $group) {
            if ($group['id']) {
                // Delete from array because id is still present
                if (($key = array_search($group['id'], $currentIds)) !== false) {
                    unset($currentIds[$key]);
                }
                $cg = ConsentGroup::findOne(['id' => $group["id"]]);
            } else {
                $cg = new ConsentGroup();
            }
            
            $cg->enabled = (!empty($group["enabled"]) ? $group["enabled"] : 0);
            $cg->defaultValue = (!empty($group["defaultValue"]) ? $group["defaultValue"] : 0);
            $cg->required = (!empty($group["required"]) ? $group["required"] : 0);

            $cg->handle = $group["handle"];
            $cg->name = $group["name"];
            $cg->desc = $group["desc"];

            if (!$cg->validate()) {
                throw new \Exception(json_encode($cg->errors));
            }
            $cg->save();
        }

        if (count($currentIds) > 0) {
            $deleteIds = implode(', ', $currentIds);
            ConsentGroup::deleteAll('id IN (' . $deleteIds . ')');
        }
        
        return true;
    }
}