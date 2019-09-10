![Screenshot](resources/img/plugin-logo.png)

# Craft Cookie consent plugin for Craft CMS 3.x
Allow your visitors to set there cookie preference which you can use to enable features in your site.
The plugin offers the following features:

Create your own cookie types:

![Create own cookie types](resources/img/cookie-type-settings.png)


- Default responsive modal
    - Position preferenc
    - Auto accept after x seconds
    - block usage without consents

Modal with settings:

![Modal with settings](resources/img/modal-with-settings.png)

Modal settings:

![Modal without settings](resources/img/modal-settings.png)

Modal without settings:

![Modal without settings](resources/img/modal-without-settings.png)

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
### Display the consent modal
`craft.craftCookieConsent.askConsent(options, force)` is used to display the default consent modal.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|options|object|false|Accepts `position` ('`top` or `bottom`-`left` or `right`')|
|force|boolean|false|Force the modal|

#### Examples
Display the modal
```craft.craftCookieConsent.getConsents()```

Display the modal in the top right corner
```craft.craftCookieConsent.getConsents({'position': 'top-right'})```

Display the modal *always*
```craft.craftCookieConsent.getConsents({}, true)```

### Get all visitor consents
`craft.craftCookieConsent.getConsents(defaultConcentIfNotSet)` is used to get an array with the visitors consents.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|defaultConcentIfNotSet|boolean|false|return the default consents if the visitor doens't have any consents|

#### Examples
Get all consents of the current visitor
``craft.craftCookieConsent.getConsents()``

Get all consents of the current visitor. Get default values if noting found.
```craft.craftCookieConsent.getConsents(true)```


### Get consent by handle
`craft.craftCookieConsent.isConsentWith(handle)` is used to get the consent of the current visitor by handle.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|handle|string|true|Use a handle from the settings|

#### Examples
Get all consents of the current visitor
```
{% if craft.craftCookieConsent.isConsentWith('marketing') %}
    We have permission to do marketing stuf
{% endif %}
```

### Custom modal
`craft.craftCookieConsent.getConsentsRaw` will return all raw data which you can use to create a custom modal.



## Craft Cookie consent Roadmap

Some things to do, and ideas for potential features:

* Release it
* More modal options / templates

Brought to you by [Dutch Height](www.dutchheight.com)
