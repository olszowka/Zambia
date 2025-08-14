//  Created by Peter Olszowka on 2020-04-24;
//  Copyright (c) 2020-2024 Peter Olszowka. All rights reserved. See copyright document for more details.

var ForgotPasswordResetForm = function() {
    this.initialize = function initialize() {
        //called when page has loaded
        this.passwordHidden = true;

        //initialize selectors
        var $submitButton = document.getElementById('submit-button');
        if (!$submitButton) {
            return;
        }
        this.$submitButton = $submitButton;

        var $passwordInput = document.getElementById('password');
        if (!$passwordInput) {
            return;
        }
        this.$passwordInput = $passwordInput;

        var $cpasswordInput = document.getElementById('cpassword');
        if (!$cpasswordInput) {
            return;
        }
        this.$cpasswordInput = $cpasswordInput;

        var $revealPassword = document.getElementById('revealPassword');
        if (!$revealPassword) {
            return;
        }
        this.$revealPassword = $revealPassword;

        var $inputControlGroupsNL = document.getElementsByClassName('control-group-input');
        this.$inputControlGroupsNL = $inputControlGroupsNL;
    
        //initialize state and event handlers
        $submitButton.disabled = true;
        var that = this;
        $passwordInput.addEventListener('input', function() {
            that.onInputEither();
        });
        $cpasswordInput.addEventListener('input', function() {
            that.onInputEither();
        });
        $revealPassword.addEventListener('click', function() {
            that.onClickRevealPassword();
        });
    };

    this.onInputEither = function onInputEither() {
        var password = this.$passwordInput.value;
        var cpassword = this.$cpasswordInput.value;
        if (!password || !cpassword) {
            this.$passwordInput.classList.remove('is-invalid');
            this.$cpasswordInput.classList.remove('is-invalid');
            this.clearErrors(); // enables submit button
            this.$submitButton.disabled = true;
            return;
        }
        if (password.length < 6) {
            this.$passwordInput.classList.add('is-invalid');
            this.$cpasswordInput.classList.remove('is-invalid');
            this.$submitButton.disabled = true;
            return;
        } else {
            this.$passwordInput.classList.remove('is-invalid');
        }
        if (password !== cpassword) {
            this.$cpasswordInput.classList.add('is-invalid');
            this.$submitButton.disabled = true;
            return;
        }
        this.$passwordInput.classList.remove('is-invalid');
        this.$cpasswordInput.classList.remove('is-invalid');
        this.$submitButton.disabled = false;
    };

    this.onClickRevealPassword = function onClickRevealPassword() {
        this.$passwordInput.setAttribute('type', this.passwordHidden ? 'text' : 'password');
        this.$cpasswordInput.setAttribute('type', this.passwordHidden ? 'text' : 'password');
        this.passwordHidden = !this.passwordHidden;
        this.$revealPassword.blur();
    }

};

var forgotPasswordResetForm = new ForgotPasswordResetForm();

/* This file should be included only on relevant page.  See main.js and javascript_functions.php */
document.addEventListener('DOMContentLoaded', forgotPasswordResetForm.initialize.bind(forgotPasswordResetForm));
