<?php


namespace dutchheight\cookieboss\controllers;

use dutchheight\cookieboss\CookieBoss;
use dutchheight\cookieboss\models\Settings;
use dutchheight\cookieboss\services\ConsentGroupService;

use Craft;
use craft\web\Controller;

use yii\web\Cookie;
use yii\web\Response;

/**
 * @author    Dutch Height
 * @package   CookieBoss
 * @since     1.0.0
 */
class ConsentController extends Controller
{

    protected $allowAnonymous = array('save-consent-settings');

    public function actionSaveConsentSettings() {
        $this->requireAcceptsJson();
        $this->requirePostRequest();

        $originalData = ConsentGroupService::getAllEnabled();
        $groups = Craft::$app->getRequest()->getRequiredParam('groups');
        $currentCookieBosss = [];

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
            $currentCookieBosss[$handle] = boolval($allowed);
        }

        $cookieData = json_encode($currentCookieBosss);

        $cookies = Craft::$app->response->cookies;
        $cookies->remove('cookie-boss');
        $cookies->add(new Cookie([
            'name' => 'cookie-boss',
            'value' => json_encode($currentCookieBosss),
            'expire' => time() + CookieBoss::$settings->cookieTime
        ]));

        return $cookieData;
    }
}