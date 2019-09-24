/**
 * Craft Cookie boss plugin for Craft CMS
 *
 * Craft Cookie boss JS
 *
 * @author    Dutch Height
 * @copyright Copyright (c) 2019 Dutch Height
 * @link      www.dutchheight.com
 * @package   CookieBoss
 * @since     1.0.0
 */

Vue.component('consent-group', {
    props: ['item', 'checkedIconPath', 'lockedIconPath'],
    data: function() {
        return {
            isActive: (this.item.hasAnyConsent && this.item.hasConsent) || (!this.item.hasAnyConsent && this.item.defaultValue),
        }
    },
    methods: {
        toggleGroup: function() {
            if (!this.item.required) {
                this.isActive = !this.isActive;
                this.$emit('toggled', { 'handle': this.item.handle, 'defaultValue': this.isActive });
            }
        }
    },
    template: `
        <div class="consent-group" v-on:click="toggleGroup()" v-bind:class="{'isToggled': isActive}">
            <div style="width: 100%; margin-top: 0; display: flex; flex-wrap: wrap; justify-content: space-between;">
                <h4 class="item-wrapper-padding noselect">{{ item.name }}</h4>
                <img v-show="item.required" class="locked-icon noselect" :src="lockedIconPath" alt="check-icon">
                <img v-show="isActive && !item.required" class="checked-icon noselect" :src="checkedIconPath" alt="check-icon">
            </div>
            <p class="item-wrapper item-wrapper-padding noselect" style="width: 100%; margin-top: 0;">{{ item.desc }}</p>
        </div>
    `
});

var CookieBoss = new Vue({
    el: '#cookie-boss',
    props: ['hideAfter'],
    delimiters: ['${', '}'],
    data: {
        timer: '',
        show: true,
        forceAccept: false,
        settingsOpen: false,
        toggled: {}
    },
    created: function () {
        var autoHide = window.cookieBossHideAfter;
        if (autoHide > 0) {
            this.timer = setTimeout(this.accept, autoHide * 1000);
        }
    },
    beforeDestroy () {
        clearInterval(this.timer);
    },
    methods: {
        toggleForceAccept: function(force) {
            this.forceAccept = force;
        },
        toggle: function() {
            this.settingsOpen = !this.settingsOpen;
        },
        accept: function() {
            clearInterval(this.timer);
            this.forceAccept = false;
            this.settingsOpen = false;
            this.show = false;

            var data = {
                'groups': this.toggled,
                [window.csrfParam]: window.csrfToken
            };

            axios({ method: 'POST', url: '/cookie-boss/save-consent-settings', data: data}).then((data) => {
                window.location.reload();
            });
        },
        toggledEvent: function(event) {
            this.toggled[event.handle] = event.defaultValue;
        }
    },
    watch: {
        forceAccept: function() {
            document.documentElement.style.overflow = (this.forceAccept) ? 'hidden' : 'auto';
        }
      }
});