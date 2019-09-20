<?php

namespace dutchheight\cookieboss\services;

use Craft;
use craft\base\Component;
use dutchheight\cookieboss\records\ConsentGroup;

class ConsentService extends Component {

    /**
     *
     * @return Boolean
     */
    public static function isConsentWith($handle, $concentIfNotSet = false) {
        if (!ConsentService::hasConsentCookie()) {
            return false;
        }

        $cookies = ConsentService::getConsentCookies();
        $settings = json_decode($cookies->value, true);
        if (!key_exists($handle, $settings)) {
            return $concentIfNotSet;
        }
        return $settings[$handle];
    }

    /**
     *
     * @return Boolean
     */
    public static function hasConsentCookie() {
        return Craft::$app->getRequest()->getCookies()->has('cookie-boss');
    }

    /**
     *
     * @return Array
     */
    public static function getConsentCookies() {
        return Craft::$app->getRequest()->getCookies()->get('cookie-boss');
    }
}