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
    [ "Magento_Checkout/js/view/sidebar", "uiRegistry", "Magento_Checkout/js/model/step-navigator", "ko", "underscore"],
    function (sidebar, uiRegistry, stepNavigator, ko, _) {
        "use strict";
        stepNavigator.isProcessed = _.wrap(stepNavigator.isProcessed, function (origin, code) {
            if (code == "shipping") {
                return true;
            } else {
                return origin(code);
            }
        });

        return sidebar.extend({
            regions: ko.observableArray(),
            isVisible: ko.observable(false),

            initialize: function () {
                this._super();

                uiRegistry.async("checkout.sidebar")(
                    function (sidebar) {
                       sidebar.elems.subscribe(function (elems) {
                           this.regions(elems);
                           this.showTotals();
                       }.bind(this), true, "change");
                    }.bind(this)
                );

            },

            showTotals: function () {
                 uiRegistry.async("checkout.steps.billing-step.payment")(
                    function (paymentStep) {
                        _.each(stepNavigator.steps(), function (step) {
                            if (step.code == 'shipping') {
                                step.isVisible(true);
                            }
                        });
                        paymentStep.isVisible(true);
                        this.isVisible(true);
                    }.bind(this)
                );
            }
        });
    }
);
