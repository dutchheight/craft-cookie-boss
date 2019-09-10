<?php

namespace dutchheight\craftcookieconsent\controllers;

use dutchheight\craftcookieconsent\CraftCookieConsent;
use dutchheight\craftcookieconsent\models\Settings;

use Craft;
use craft\web\Controller;
use craft\helpers\UrlHelper;
use craft\records\Element;
use craft\elements\Entry;
use dutchheight\craftcookieconsent\services\ConsentTypeService;
use yii\web\Response;
use yii\web\NotFoundHttpException;


/**
 * @author    Dutch Height
 * @package   CraftCookieConsent
 * @since     1.0.0
 */
class SettingsController extends Controller
{

    public function actionPluginSettings($settings = null): Response
    {
        $variables = [];

        if (!$settings) {
            $settings = CraftCookieConsent::$settings;
        }

        $variables['crumbs'] = [
            ['label' => Craft::t('app', 'Settings'), 'url' => UrlHelper::cpUrl('settings')]
        ];

        // Reformat to days
        $settings['cookieTime'] = $settings['cookieTime'] / 86400;
        $settings['cookiesPageId'] = Entry::findOne(['id' => $settings['cookiesPageId']]);

        $variables['settings'] = $settings;
        $variables['tabs'] = [
            'general' => [
                'label' => Craft::t('app', 'General'),
                'url' => '#list-general'
            ],
            'popup' => [
                'label' => Craft::t('craft-cookie-consent', 'Popup'),
                'url' => '#list-popup'
            ],
            'consent-types' => [
                'label' => Craft::t('craft-cookie-consent', 'Consent types'),
                'url' => '#list-consent-types'
            ]
        ];

        return $this->renderTemplate('craft-cookie-consent/settings', $variables);
    }


    /**
     * Saves the general plugin settings.
     *
     * @return yii\web\Response|null
     * @throws yii\web\ForbiddenHttpException if the current user does not have the right permission
     * @throws yii\web\NotFoundHttpException if the requested plugin cannot be found.
     */
    public function actionSaveGeneral()
    {
        $this->requirePostRequest();
        $plugin = Craft::$app->getPlugins()->getPlugin('craft-cookie-consent');

        if (!$plugin) {
            throw new NotFoundHttpException(Craft::t('app', 'Plugin not found.'));
        }

        $consentTypes = Craft::$app->getRequest()->getRequiredBodyParam('cookieTypes');
        if (!$consentTypes) {
            $this->error($plugin);
            return null;
        }

        $update = ConsentTypeService::updateAll($consentTypes);
        if (is_array($update)) {
            $this->error($plugin);
            return null;
        }

        $settings['enabled']            = (Craft::$app->getRequest()->getRequiredBodyParam('enabled') == '1');
        $settings['presentTypes']       = (Craft::$app->getRequest()->getRequiredBodyParam('presentTypes') == '1');
        $settings['forceAccept']        = (Craft::$app->getRequest()->getRequiredBodyParam('forceAccept') == '1');
        
        $settings['cookieTime']         = Craft::$app->getRequest()->getRequiredBodyParam('cookieTime') * 86400;
        $settings['title']              = Craft::$app->getRequest()->getRequiredBodyParam('title');
        $settings['message']            = Craft::$app->getRequest()->getRequiredBodyParam('message');
        $settings['acceptButtonText']   = Craft::$app->getRequest()->getRequiredBodyParam('acceptButtonText');
        $settings['settingsButtonText'] = Craft::$app->getRequest()->getRequiredBodyParam('settingsButtonText');
        $settings['cookiesPageId']      = Craft::$app->getRequest()->getRequiredBodyParam('contactPage');
        $settings['acceptAfterSeconds'] = Craft::$app->getRequest()->getRequiredBodyParam('acceptAfterSeconds');

        $success = Craft::$app->getPlugins()->savePluginSettings($plugin, $settings);
        if (!$success) {
            $this->error($plugin);
            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));
        return $this->redirectToPostedUrl();
    }

    private function error($plugin) {
        Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));
        // Send the plugin back to the template
        Craft::$app->getUrlManager()->setRouteParams([
            'plugin' => $plugin,
        ]);
    }
}