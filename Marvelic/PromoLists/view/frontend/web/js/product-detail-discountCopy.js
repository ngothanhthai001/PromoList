define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Ui/js/modal/modal'
], function($,ko, Component, modal){
    "use strict";
    var titleDiscount = ".promolist-discount-item";
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
    $(titleDiscount).on('click',function(){
        modal(options, $(modalContent));
        $(modalContent).modal("openModal");

    });

    return Component.extend({
        defaults: {
            template: 'Marvelic_PromoLists/product-detail-discount',
        },


        initialize: function () {
            this._super();
            this.discountListArray= ko.observableArray([]);
            if(this.discountList){
                this.discountListArray(this.discountList);
            }
            return this;
        },
        showDiscount: function (){
            return this.discountListArray();
        }
    });

});
