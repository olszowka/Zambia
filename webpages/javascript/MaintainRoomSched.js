//  Created by Peter Olszowka on 2015-08-30;
//  Copyright (c) 2015-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.

var MaintainRoomSched = function() {
    this.roomArr = [];

    this.onChangeShowUnscheduledRooms = function () {
        var that = this;
        if (this.$showUnscheduledRoomsCheckbox.checked) {
            this.$roomsSelect.querySelectorAll("option:not([value='0'])")
                .forEach(function (elem) {
                    elem.remove();
                });
            this.roomArr.forEach(function (room) {
                var option = document.createElement('option');
                option.setAttribute('value', room.value);
                option.innerText = room.text;
                option.dataset.isScheduled = room.isScheduled;
                that.$roomsSelect.appendChild(option);
            });
        } else {
            this.$roomsSelect.querySelectorAll("option[data-is-scheduled='0']")
                .forEach(function (elem) {
                    elem.remove();
                });
        }
    };

    this.initialize = function () {
        //called when My Profile page has loaded
        var that = this;
        this.$showUnscheduledRoomsCheckbox = document.getElementById('showUnschedRmsCHK');
        this.$roomsSelect = document.getElementById('selroom');
        this.$showUnscheduledRoomsCheckbox.addEventListener('change', this.onChangeShowUnscheduledRooms.bind(this));
        this.$roomsSelect.querySelectorAll("option:not([value='0'])").forEach(function (option) {
            that.roomArr.push({
                value: option.getAttribute('value'),
                text: option.innerText,
                isScheduled: option.dataset.isScheduled
            });
        });
        this.onChangeShowUnscheduledRooms();
        var $addToScheduleTable = document.getElementById('add-to-room-schedule-table');
        if ($addToScheduleTable) {
            $addToScheduleTable.querySelectorAll('.room-select-td > select').forEach(function(elem) {
                new Choices(elem, {
                    searchResultLimit: 9999,
                    searchPlaceholderValue: "Type here to search list."
                });
            });
        }
    };
};

var maintainRoomSched = new MaintainRoomSched();

/* This file should be included only on relevant page.  See main.js and javascript_functions.php */
document.addEventListener('DOMContentLoaded', maintainRoomSched.initialize.bind(maintainRoomSched));
