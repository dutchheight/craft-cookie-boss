<?php

namespace dutchheight\craftcookieconsent\services;


use Craft;
use craft\base\Component;
use dutchheight\craftcookieconsent\records\ConsentType;

class ConsentTypeService extends Component {
    
    public static function getAll() {
        return ConsentType::find()->orderBy('id')->all();
    }
    
    public static function getAllEnabled() {
        return ConsentType::find()->where(['enabled' => 1])->orderBy('id')->all();
    }

    public static function deleteAll() {
        return ConsentType::deleteAll();
    }

    public static function updateAll(Array $types) {
        ConsentTypeService::deleteAll();

        foreach ($types as $type) {
            $ct = new ConsentType();
            
            $ct->enabled = (!empty($type["enabled"]) ? $type["enabled"] : 0);
            $ct->defaultValue = (!empty($type["defaultValue"]) ? $type["defaultValue"] : 0);
            $ct->required = (!empty($type["required"]) ? $type["required"] : 0);

            $ct->handle = $type["handle"];
            $ct->name = $type["name"];
            $ct->desc = $type["desc"];

            if (!$ct->validate()) {
                throw new \Exception(json_encode($ct->errors));
            }

            $ct->save();
        }
        
        return true;
    }
}