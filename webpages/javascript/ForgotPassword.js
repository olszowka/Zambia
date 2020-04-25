//	Created by Peter Olszowka on 2020-04-24;
//	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.

var ForgotPassword = function() {
    this.initialize = function initialize() {
        //called when page has loaded

        //initialize selectors
        var $submitButton = document.getElementById('submit-button');
        if (!$submitButton) {
            return;
        }
        this.$submitButton = $submitButton;

        var $badgeInput = document.getElementById('badgeid');
        if (!$badgeInput) {
            return;
        }
        this.$badgeInput = $badgeInput;

        var $emailAddressInput = document.getElementById('emailAddress');
        if (!$emailAddressInput) {
            return;
        }
        this.$emailAddressInput = $emailAddressInput;

        //initialize state and event handlers
        $submitButton.disabled = true;
        var that = this;
        $badgeInput.addEventListener('input', function() {
            that.onInputEither();
        });
        $emailAddressInput.addEventListener('input', function() {
            that.onInputEither();
        });
    };

    this.onInputEither = function onInputEither() {
        this.$submitButton.disabled = (this.$badgeInput.value === '' || this.$emailAddressInput.value === '');
    };

};

var forgotPassword = new ForgotPassword();

/* This file should be included only on relevant page.  See main.js and javascript_functions.php */
document.addEventListener('DOMContentLoaded', forgotPassword.initialize.bind(forgotPassword));
