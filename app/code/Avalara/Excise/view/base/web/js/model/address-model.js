
define(
    ['ko'],
    function (ko) {
        'use strict';
        var originalAddress = ko.observable(null);
        var validAddress = ko.observable(null);
        var selectedAddress = ko.observable(null);
        var error = ko.observable(null);
        var isDifferent = ko.observable(null);
        return {
            originalAddress: originalAddress,
            validAddress: validAddress,
            selectedAddress: selectedAddress,
            error: error,
            isDifferent: isDifferent,
            resetValues: function () {
                this.originalAddress(null);
                this.validAddress(null);
                this.selectedAddress(null);
                this.error(null);
                this.isDifferent(null);
            }
        };
    }
);
