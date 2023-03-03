define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Ui/js/modal/modal'
], function($,ko, Component, modal){
    "use strict";

    return Component.extend({
        defaults: {
            template: 'Marvelic_PromoLists/product-detail-discount',
        },
        discountListArray : ko.observable([]),
        contentPopupCoupon: ko.observable(),

        initialize: function () {
            this._super();
            this.discountListArray= ko.observable(this.discountList);
            // this.contentPopupCoupon = ko.observable(null);
            return this;
        },
        /**
         * Init observable variables
         *
         * @return {Object}
         */
        // initObservable: function () {
        //     var self = this;
        //     return self;
        // },

        showPopupDiscount: function (data,elem){
            var modalContent = "#modal-content";
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: [{
                    text: $.mage.__('Continue'),
                    class: 'modal-discount',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };
            this.contentPopupCoupon(data);
            modal(options, $(modalContent));
            $(modalContent).modal("openModal");
        }
    });

});
