# Craft Cookie consent plugin for Craft CMS 3.x

Allow your visitors to set there cookie preference.

![Screenshot](resources/img/plugin-logo.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require https://github.com/dutchheight/craft-cookie-consent

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft Cookie consent.

## Craft Cookie consent Overview

-Insert text here-

## Configuring Craft Cookie consent

-Insert text here-

## Using Craft Cookie consent

-Insert text here-

{{ craft.craftCookieConsent.askConsent() }}

craft.craftCookieConsent.getConsents()
{{ dump(craft.craftCookieConsent.getConsentsRaw) }}

{% if craft.craftCookieConsent.isConsentWith('marketing') %}
    COOKIEEEES SNAACKKSSS
{% endif %}

## Craft Cookie consent Roadmap

Some things to do, and ideas for potential features:

* Release it

Brought to you by [Dutch Height](www.dutchheight.com)
