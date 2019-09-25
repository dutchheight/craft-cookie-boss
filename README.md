![Screenshot](resources/img/plugin-logo.png)

# Craft Cookie boss plugin for Craft CMS 3.x
Allow your visitors to set there cookie preference which you can use to enable features in your site.
Create your own cookie groups, make them required and/or set there default state.
These cookie groups can contain cookies with information which you can display to describe the purpos of each cookie.

![Create own cookie groups](resources/img/consent-groups.png)
![Add cookies to cookie groups](resources/img/cookie-descriptions.png)

The default modal contains the following features:
- Default responsive modal
- Position (top-left, top-right, bottom-left, bottom-right)
- Auto accept and close after x seconds (optional)
- Block site usage without consent
- Display settings (yes/no)

Modal with settings:

![Modal with settings](resources/img/modal-with-settings.png)

Modal settings:

![Modal without settings](resources/img/modal-settings.png)

Modal without settings:

![Modal without settings](resources/img/modal-without-settings.png)

## Requirements
This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require https://github.com/dutchheight/craft-cookie-boss

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft Cookie boss.

4. Add `{{ craft.cookieBoss.askConsent() }}` on the page(s) which should display the modal.

## Using Craft Cookie boss
### Display the consent modal
`craft.cookieBoss.askConsent(options, force)` is used to display the default consent modal.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|options|object|false|Accepts `position` ('`top` or `bottom`-`left` or `right`')|
|force|boolean|false|Force the modal|

#### Examples
Display the modal
```craft.cookieBoss.getConsents()```

Display the modal in the top right corner
```craft.cookieBoss.getConsents({'position': 'top-right'})```

Display the modal always:
```
{{ craft.cookieBoss.getConsents({}, true) }}
```
---

### Display cookie descriptions
Display a table with all enabled cookies. The table has `#cookie-descriptions` as id.
Eache cookie is provided with the class `.consent-true` or `.consent-false` depending on the consentgroup's consent.
All cookies are grouped by there group which has the class `.cookie-descriptions-group-title`. If you like more control you can use `craft.cookieBoss.getCookiesRaw()`.

#### Examples
Display a table with all enabled cookies
```
{{ craft.cookieBoss.getCookies() }}
```
---

### Display cookie group toggle checkbox
Display's a checkbox which toggles the specified consent group. For more control see 'Toggle consent group with a form'

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|handle|string|true|Use a handle from the settings|

#### Examples
Display a checkbox which can be used to toggle a consent group
```
{{ craft.cookieBoss.toggleConsentGroupForm('marketing') }}
```
---

### Get all visitor consents
`craft.cookieBoss.getConsents(defaultConcentIfNotSet)` is used to get an array with the visitors consents.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|defaultConcentIfNotSet|boolean|false|return the default consents if the visitor doens't have any consents|

#### Examples
Get all consents of the current visitor
``craft.cookieBoss.getConsents()``

Get all consents of the current visitor. Get default values if noting found.
```craft.cookieBoss.getConsents(true)```

---

### Get if visitor gave consent for cookie group
`craft.cookieBoss.isConsentWith(handle)` is used to get the consent of the current visitor by handle.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|handle|string|true|Use a handle from the settings|

#### Examples
Get consents of the current visitor
```
{% if craft.cookieBoss.isConsentWith('marketing') %}
    We have permission to do marketing stuf
{% endif %}
```
---

### Get all consent groups
`craft.cookieBoss.getConsentsGroupsRaw(false)` is used to get all consent groups.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|onlyEnabled|boolean|false|Only return enabled consent groups|

#### Examples
Get all consent groups.
```
craft.cookieBoss.getConsentsGroupsRaw()
```
---

### Get all consent groups by handle
`craft.cookieBoss.getConsentsGroupRawByHandle(consentGroupHandle)` is used to get all cookie groups by handle.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|consentGroupHandle|string|true|Use a handle from the settings|

#### Examples
Get all cookie groups by handle.
```
craft.cookieBoss.getConsentsGroupRawByHandle()
```
---

### Get all cookie descriptions
`craft.cookieBoss.getCookiesRaw()` is used to get all cookie descriptions.
`craft.cookieBoss.getCookiesRaw(consentGroupHandle)` is used to get all cookie descriptions for a consent group.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|consentGroupHandle|string|true|Use a handle from the settings|

#### Examples
Get all cookie descriptions
```
craft.cookieBoss.getCookiesRaw()
```
---

### Custom modal
`craft.cookieBoss.getConsentsGroupsRaw()` will return all raw data which you can use to create a custom modal.

### Save the settings
`/craft-cookie-boss/save-consent-settings` accepts `POST` requests with new consent settings.
Usage of the Craft csrf token is required. Use `craft.app.request.csrfParam` to get the key and `craft.app.request.csrfToken` to get the actual token.

| Attribute | Type | Required | Description |
|:----------|:-----|:---------|:------------|
|toggled|object|true|Use a handle from the settings|
|`csrfParam`|string|true|Craft csrf token. Use the `csrfParam` as key|

#### Example request data
```
    "groups": {
        "technical": true,
        "marketing": false
    },
    "CRAFT_CSRF_TOKEN": "ABC...XYZ"
```
---

### Toggle a consent group with a custom form
Do a `POST` request to `/cookie-boss/toggle-consent-group`.
For each group you like to toggle you need to pass a boolean represented by the handle name.

#### Example form
```
{% set handle = 'marketing %}
{% set consentGroup = craft.cookieBoss.getConsentsGroupRawByHandle(handle) %}
<form id="toggle-{{ handle }}-form" action="/cookie-boss/toggle-consent-group" method="POST">
    {{ csrfInput() }}
    {{ redirectInput(craft.request.getPath()) }}
    <input type="hidden" name="groups[marketing]" value="" />
    <input type="checkbox" class="inline js-toggle-consentGroup ml-2" value="true"
        name="groups[{{ handle }}]"
        onchange="document.getElementById('toggle-{{ handle }}-form').submit();"
        id="toggle-{{ handle }}-checkbox"
        {% if consentGroup.required %} disabled {% endif %}
        {% if consentGroup.hasConsent() or consentGroup.required %}checked {% endif %}>
</form>
```
---

## Craft Cookie Boss Roadmap
Some things to do, and ideas for potential features:

* More templates (Modals)
* Support for different settings per site (multisite)
* Javascript events

Brought to you by [Dutch Height](www.dutchheight.com)
