<?php


namespace dutchheight\craftcookieconsent\controllers;

use dutchheight\craftcookieconsent\CraftCookieConsent;
use dutchheight\craftcookieconsent\models\Settings;
use dutchheight\craftcookieconsent\services\ConsentTypeService;

use Craft;
use craft\web\Controller;

use yii\web\Cookie;
use yii\web\Response;

/**
 * @author    Dutch Height
 * @package   CraftCookieConsent
 * @since     1.0.0
 */
class ConsentController extends Controller
{

    protected $allowAnonymous = array('save-consent-settings');

    public function actionSaveConsentSettings() {
        $this->requireAcceptsJson();
        $this->requirePostRequest();

        $originalData = ConsentTypeService::getAllEnabled();
        $toggled = Craft::$app->getRequest()->getRequiredParam('toggled');
        $currentCookieConsents = [];

        foreach ($originalData as $consentsType) {
            $handle = $consentsType->handle;
            $allowed = false;

            if (key_exists($handle, $toggled)) {
                $allowed = $toggled[$handle];
            } else {
                $allowed = $consentsType->defaultValue;
            }

            if ($consentsType->required) {
                $allowed = true;
            }
            $currentCookieConsents[$handle] = boolval($allowed);
        }

        $cookieData = json_encode($currentCookieConsents);
        
        $cookies = Craft::$app->response->cookies;
        $cookies->remove('craft-cookie-consent');
        $cookies->add(new Cookie([
            'name' => 'craft-cookie-consent',
            'value' => json_encode($currentCookieConsents),
            'expire' => time() + CraftCookieConsent::$settings->cookieTime
        ]));

        return $cookieData;
    }
}