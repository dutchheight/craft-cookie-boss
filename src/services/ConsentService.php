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
    public function isConsentWith($handle, $concentIfNotSet = false) {
        if (!$this->hasConsentCookie()) {
            return false;
        }

        $cookies = $this->getConsentCookies();
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
    public function hasConsentCookie() {
        return Craft::$app->getRequest()->getCookies()->has('cookie-boss');
    }

    /**
     *
     * @return Array
     */
    public function getConsentCookies() {
        return Craft::$app->getRequest()->getCookies()->get('cookie-boss');
    }

    public function generateCookieData($groups) {
        $originalData = CookieBoss::getInstance()->consentGroups->getAllEnabled();
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
    public function saveConsentCookies($data) {
        $cookies = Craft::$app->response->cookies;
        $cookies->remove('cookie-boss');
        $cookies->add(new Cookie([
            'name' => 'cookie-boss',
            'value' => json_encode($data),
            'expire' => time() + CookieBoss::$settings->cookieTime
        ]));
    }
}