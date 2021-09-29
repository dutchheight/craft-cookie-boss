<?php

namespace dutchheight\cookieboss\controllers;

use Craft;

use craft\elements\Entry;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use dutchheight\cookieboss\CookieBoss;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * @package CookieBoss
 * @author  Dutch Height <info@dutchheight.com>
 * @since   1.0.0
 */
class SettingsController extends Controller
{

    public function actionPluginSettings($settings = null): Response
    {
        $variables = [];

        if (!$settings) {
            $settings = CookieBoss::$settings;
        }

        $variables['crumbs'] = [
            ['label' => Craft::t('app', 'Settings'), 'url' => UrlHelper::cpUrl('settings')]
        ];

        // Reformat to days
        $settings['cookieTime'] = $settings['cookieTime'] / 86400;
        if (!is_null($settings['cookiesPageId'])) {
            $settings['cookiesPageId'] = Entry::findOne(['id' => $settings['cookiesPageId']]);
        }

        $variables['consentGroups'] = CookieBoss::getInstance()->consentGroups->getAll();
        $variables['consentGroupsSelectOptions'] = CookieBoss::getInstance()->consentGroups->getAllAsSelectOptions();
        $variables['cookies'] = CookieBoss::getInstance()->cookieDescriptions->getAll();
        
        $variables['allowAdminChanges'] = Craft::$app->getConfig()->getGeneral()->allowAdminChanges;
        $variables['settings'] = $settings;
        $variables['tabs'] = [
            'general' => [
                'label' => Craft::t('app', 'General'),
                'url' => '#list-general'
            ],
            'popup' => [
                'label' => Craft::t('cookie-boss', 'Popup'),
                'url' => '#list-popup'
            ],
            'consent-groups' => [
                'label' => Craft::t('cookie-boss', 'Consent groups'),
                'url' => '#list-consent-groups'
            ],
            'cookies' => [
                'label' => Craft::t('cookie-boss', 'Cookies'),
                'url' => '#list-cookies'
            ]
        ];

        return $this->renderTemplate('cookie-boss/settings', $variables);
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
        $plugin = Craft::$app->getPlugins()->getPlugin('cookie-boss');

        if (!$plugin) {
            throw new NotFoundHttpException(Craft::t('app', 'Plugin not found.'));
        }

        $forceReconsent = (bool)Craft::$app->getRequest()->getBodyParam('reset-consent');
        return $this->_saveSettings($forceReconsent);
    }

    /**
     * Save plugin settings
     *
     * @param boolean $forceReconsent Show the plugin new consenses?
     *
     * @return void
     */
    private function _saveSettings($forceReconsent = false)
    {
        $plugin     = Craft::$app->getPlugins()->getPlugin('cookie-boss');
        $cookies    = Craft::$app->getRequest()->getRequiredBodyParam('cookies');
        $cookies    = empty($cookies) ? [] : $cookies;
        $update     = CookieBoss::getInstance()->cookieDescriptions->updateAll($cookies);

        if (is_array($update)) {
            $this->_error();
            return null;
        }

        $consentGroups  = Craft::$app->getRequest()->getRequiredBodyParam('consentGroups');
        $consentGroups  = empty($consentGroups) ? [] : $consentGroups;
        try {
            $update = CookieBoss::getInstance()
                ->consentGroups
                ->updateAll($consentGroups);
        } catch(Exception $e) {
            $this->_error();
            return $this->redirectToPostedUrl();
        }

        if (is_array($update)) {
            $this->_error();
            return null;
        }

        

        $settings['enabled']                    = (Craft::$app->getRequest()->getRequiredBodyParam('enabled') == '1');
        $settings['presentGroups']              = (Craft::$app->getRequest()->getRequiredBodyParam('presentGroups') == '1');
        $settings['forceAccept']                = (Craft::$app->getRequest()->getRequiredBodyParam('forceAccept') == '1');

        $cookieTime                             = (Craft::$app->getRequest()->getRequiredBodyParam('cookieTime') != null) ? 
            Craft::$app->getRequest()->getRequiredBodyParam('cookieTime') : 1;
        $settings['cookieTime']                 = $cookieTime * 86400;
        
        $settings['title']                      = Craft::$app->getRequest()->getRequiredBodyParam('title');
        $settings['message']                    = Craft::$app->getRequest()->getRequiredBodyParam('message');
        $settings['messageSettings']            = Craft::$app->getRequest()->getRequiredBodyParam('messageSettings');
        $settings['acceptButtonText']           = Craft::$app->getRequest()->getRequiredBodyParam('acceptButtonText');
        $settings['acceptButtonSettingsText']   = Craft::$app->getRequest()->getRequiredBodyParam('acceptButtonSettingsText');
        $settings['settingsButtonText']         = Craft::$app->getRequest()->getRequiredBodyParam('settingsButtonText');
        $settings['cookiesPageId']              = Craft::$app->getRequest()->getRequiredBodyParam('contactPage');
        $settings['acceptAfterSeconds']         = Craft::$app->getRequest()->getRequiredBodyParam('acceptAfterSeconds');
        $settings['position']                   = Craft::$app->getRequest()->getRequiredBodyParam('position');

        if ($forceReconsent) {
            $settings['lastSettingsUpdate'] = time();
        }

        $success = Craft::$app->getPlugins()->savePluginSettings($plugin, $settings);
        if (!$success) {
            $this->_error();
            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));
        return $this->redirectToPostedUrl();
    }

    /**
     * Show Craft CMS error
     *
     * @return void
     */
    private function _error()
    {
        Craft::$app->getSession()->setError(Craft::t('app', "Couldn't save plugin settings."));
        // Send the plugin back to the template
        Craft::$app->getUrlManager()->setRouteParams([
            'plugin' => Craft::$app->getPlugins()->getPlugin('cookie-boss'),
        ]);
    }
}