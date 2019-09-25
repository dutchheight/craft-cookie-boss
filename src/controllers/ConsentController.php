<?php


namespace dutchheight\cookieboss\controllers;

use Craft;
use craft\web\Controller;

use dutchheight\cookieboss\services\ConsentService;

/**
 * @author    Dutch Height
 * @package   CookieBoss
 * @since     1.0.0
 */
class ConsentController extends Controller
{

    protected $allowAnonymous = array('save-consent-settings', 'toggle-consent-group');

    public function actionToggleConsentGroup() {
        $this->requirePostRequest();

        $body = Craft::$app->getRequest()->getBodyParams();
        ConsentService::saveConsentCookies(
            ConsentService::generateCookieData($body['groups'] ?? [])
        );

        $this->redirectToPostedUrl();
    }

    public function actionSaveConsentSettings() {
        $this->requireAcceptsJson();
        $this->requirePostRequest();

        $currentCookieBoss = ConsentService::generateCookieData(Craft::$app->getRequest()->getRequiredParam('groups'));
        ConsentService::saveConsentCookies($currentCookieBoss);

        return json_encode($currentCookieBoss);
    }
}