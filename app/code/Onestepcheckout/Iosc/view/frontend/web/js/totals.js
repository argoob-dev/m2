/**
 * OneStepCheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to One Step Checkout AS software license.
 *
 * License is available through the world-wide-web at this URL:
 * https://www.onestepcheckout.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@onestepcheckout.com so we can send you a copy immediately.
 *
 * @category   onestepcheckout
 * @package    onestepcheckout_iosc
 * @copyright  Copyright (c) 2017 OneStepCheckout  (https://www.onestepcheckout.com/)
 * @license    https://www.onestepcheckout.com/LICENSE.txt
 */
define(
    [
        "jquery",
        "uiComponent",
        "uiRegistry",
        "Magento_Ui/js/lib/view/utils/dom-observer"
    ],
    function (jQuery, uiComponent, uiRegistry, domObserver) {
        "use strict";

        return uiComponent.extend({

            initialize : function () {
                this._super();
                this.moveItems();
                this.addTitleNumber();
                this.addTitle();
            },

            /**
             * Enable summary view if promise is fulfilled from uiRegistry
             */
            addTitle : function () {
                uiRegistry.async("checkout.iosc-summary")(
                    function (summary) {
                        var selector = "#iosc-summary .opc-block-summary > span.title";
                        domObserver.get(selector, function (elem) {
                            elem.className += " step-title";
                            jQuery("#iosc-summary").prepend(elem);
                            domObserver.remove(selector);
                        });
                    }
                );
            },

            moveItems : function () {
                uiRegistry.async("checkout.sidebar.summary.cart_items")(
                    function (cartItems) {
                        var selector = "#iosc-summary  div.block.items-in-cart";
                        domObserver.get(selector, function (elem) {
                                jQuery("#iosc-summary div.opc-block-summary")
                                    .prepend(elem);
                                domObserver.remove(selector);
                            });
                        selector = "#iosc-summary  div.block.items-in-cart > div.title";
                        domObserver.get(selector, function (elem) {
                                jQuery(elem).hide();
                                domObserver.remove(selector);
                            });
                    }
                );
            },

            addTitleNumber: function () {
                uiRegistry.async("checkout.steps.billing-step.payment")(
                    function (paymentStep) {
                        var selector = "#iosc-summary span.step-title";
                        domObserver.get(selector, function (elem) {
                            jQuery(elem).prepend(jQuery("<span class='title-number'><span>&#10003;</span></span>").get(0));
                            domObserver.remove(selector);
                        });
                    }
                );
            }
        });

    }
);
