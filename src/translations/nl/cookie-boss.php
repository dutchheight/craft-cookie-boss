<?php
/**
 * Craft Cookie boss plugin for Craft CMS 3.x
 *
 * Allow your visitors to set there cookie preference.
 *
 * @link      www.dutchheight.com
 * @copyright Copyright (c) 2019 Dutch Height
 */

/**
 * Craft Cookie boss en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('craft-cookie-boss', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Dutch Height
 * @package   CookieBoss
 * @since     1.0.0
 */
return [
    'Consent groups' => 'Cookie groepen',

    'Enable Cookie Boss.' => 'Cookie Boss inschakelen',
    'Give choices immediately' => 'Geef keuze direct',
    'Give choices immediately, turn off if u want "using is accepting". Keep in mind you can present the settings on a different page.' => 'Geef keuze direct, schakel uit voor "gebruiken is toestaan".',
    'Force accept' => 'Forceer toesteming',
    'Prevent using the page until a consent is given.' => 'Schakel gebruik van de pagina uit tot de cookies toegestaan zijn.',
    'Cookie page' => 'Cookie pagina',
    'Set cookie info page.' => 'Selecteer cookie informatie pagina.',
    'Consent invalid after in days' => 'Toestemming is niet meer geldig na aantal dagen.',
    'The amount of time the consent is valid.' => 'Geldigheid in dagen.',
    'Save and invalidate consent' => 'Bewaren en opnieuw om toestemming vragen',

    'The title of the consent popup.' => 'De titel van de toesteming popup.',
    'The message of the consent popup.' => 'Het bericht van de toesteming popup.',
    'Settings message' => 'Bericht instellingen',
    'The settings message of the consent popup.' => 'Tekst bij instellingen.',
    'Accept Button' => 'Toestaan knop',
    'Accept Button settings' => 'Toestaan knop instellingen',
    'The text of the accept button.' => 'De tekst op de toestaan knop.',
    'The text of the accept button on the settings popup.' => 'De tekst op de toestaan knop op de instellingen popup.',
    'Settings Button' => 'Instellingen knop',
    'The text of the settings button.' => 'De tekst op de instellingen knop.',
    'Hide after' => 'Verbergen na',
    'Set the amount of seconds the the popup is visable. When the popup hides all default cookies are accepted. Set 0 to disable.' => 'De tijd dat de popup zichtbaar is. Na het verbergen worden alle cookies geaccepteerd. Zet op 0 om uit te schakelen.',

    'A consent group is a group of cookies for a specific purpose.' => 'Een cookie groep is een groep cookies, met een specifiek doel.',
    'The name of this consent group.' => 'De naam van deze groep.',
    'Handle to use in twig.' => 'De twig handle.',
    'The description of this group. This description is visable for your visitors.' => 'De beschrijving van deze groep. Deze beschrijving is zichtbaar voor de bezoeker.',
    'Default on' => 'Standaard aan',
    'Switches the default state.' => 'De standaard status.',
    'Required' => 'Verplicht',
    'Set to true if this consent group is required to opperate the site.' => 'Zet aan, wanneer het verplicht is deze groep toe te staan.',

    'All cookies used on this site.' => 'Alle cookies welke worden gebruikt op deze website.',
    'Consent group' => 'Cookie groep',
    'Define the consent group.' => 'Specificeer de cookie groep.',
    'Key used for this cookie.' => 'Sleutel voor deze cookie.',
    'Purpose' => 'Doel',
    'The purpose of this cookie. This description is visable for your visitors.' => 'Het doel van deze cookie. Deze beschrijving is zichtbaar voor de bezoeker.',
    'The description of this cookie. This description is visable for your visitors.' => 'De beschrijving van deze cookie. Deze beschrijving is zichtbaar voor de bezoeker.',
];