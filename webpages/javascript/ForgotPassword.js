//  Created by Peter Olszowka on 2020-04-24;
//  Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.

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
        
        var $recaptchaScript = document.getElementById('recaptcha-script');
        if (!$recaptchaScript) {
            return;
        }

        var $recaptchaErrorMessage = document.getElementById('recaptcha-error-message');
        if (!$recaptchaErrorMessage) {
            return;
        }

        
        
        //initialize state and event handlers
        $submitButton.disabled = true;
        var that = this;
        $badgeInput.addEventListener('input', function() {
            that.onInputEither();
        });
        $emailAddressInput.addEventListener('input', function() {
            that.onInputEither();
        });
        $recaptchaScript.addEventListener('error', function() {
            $recaptchaErrorMessage.classList.remove('hidden');
        })
    };

    this.onInputEither = function onInputEither() {
        this.$submitButton.disabled = (
               this.$badgeInput.value === ''
            || this.$emailAddressInput.value === ''
            || !isRecaptchaChecked
        );
    };

};

var forgotPassword = new ForgotPassword();

/* This file should be included only on relevant page.  See main.js and javascript_functions.php */
document.addEventListener('DOMContentLoaded', forgotPassword.initialize.bind(forgotPassword));

var isRecaptchaChecked = false;

function recaptchaCheckedCallback() {
    isRecaptchaChecked = true;
    forgotPassword.onInputEither();
}
