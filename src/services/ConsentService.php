<?php

namespace dutchheight\cookieboss\services;

use Craft;
use craft\base\Component;
use yii\web\Cookie;

use dutchheight\cookieboss\CookieBoss;

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

    public static function generateCookieData($groups) {
        $originalData = ConsentGroupService::getAllEnabled();
        $currentCookieBoss = [];

        foreach ($originalData as $consentsGroups) {
            $handle = $consentsGroups->handle;
            $allowed = false;

            if (key_exists($handle, $groups)) {
                $allowed = $groups[$handle];
            } else {
                $allowed = $consentsGroups->defaultValue;
            }

            if ($consentsGroups->required) {
                $allowed = true;
            }
            $currentCookieBoss[$handle] = boolval($allowed);
        }

        return $currentCookieBoss;
    }

    /**
     * @var Array ['handle' => 'boolean']
     * @return Void
     */
    public static function saveConsentCookies($data) {
        $cookies = Craft::$app->response->cookies;
        $cookies->remove('cookie-boss');
        $cookies->add(new Cookie([
            'name' => 'cookie-boss',
            'value' => json_encode($data),
            'expire' => time() + CookieBoss::$settings->cookieTime
        ]));
    }
}