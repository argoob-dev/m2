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
define([
    'jquery',
    'underscore',
    'ko',
    'uiRegistry',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/customer/address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/view/billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    "Magento_Checkout/js/model/shipping-rates-validator",
    'Onestepcheckout_Iosc/js/shared/fields',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    "Magento_Ui/js/lib/view/utils/dom-observer"
], function (
    jQuery,
    _,
    ko,
    uiRegistry,
    customer,
    customerAddress,
    quote,
    billingAddress,
    checkoutData,
    additionalValidators,
    shippingRatesValidator,
    fieldsObj,
    createBillingAddress,
    selectBillingAddress,
    setBillingAddressAction,
    globalMessageList,
    $t,
    domObserver
) {
    "use strict";

    var newAddressOption = {
            /**
             * Get new address label
             * @returns {String}
             */
            getAddressInline: function () {
                return $t('New Address');
            },
            customerAddressId: null
        };

    return billingAddress.extend({

        isBillingAddressDetailsVisible: ko.observable(false),
        isUseBillingAddress: ko.observable(false),

        shippingDomReady:ko.observable(false),
        domReady: ko.observable(false),

        dataScopePrefix: false,
        source: false,
        formObjects: false,

        initialize: function () {
            this._super();
            if (quote.isVirtual()) {
                this.isBillingAddressDetailsVisible(true);
            }
            uiRegistry.async('checkoutProvider')(
                function (checkoutProvider) {
                    this.source = checkoutProvider;
                    fieldsObj.domReadyHandler(this.lastVisibleField, this);
                }.bind(this)
            );
            this.formObjects = false;
            uiRegistry.async("checkout.steps.billing-step.payment.payments-list.free-form")(
                function (addressForm) {
                    if (!this.formObjects) {
                        this.dataScopePrefix = 'billingAddressfree';
                        this.formObjects = addressForm;
                        this.domReady
                            .subscribe(this.onDomReady.bind(this), null,'change');
                    }
                }.bind(this)
            );

            uiRegistry.async("checkout.steps.billing-step.payment.afterMethods.billing-address-form")(
                function (addressForm) {
                    if (!this.formObjects) {
                        this.dataScopePrefix = "billingAddressshared";
                        this.formObjects = addressForm;
                        this.domReady
                            .subscribe(this.onDomReady.bind(this), null,'change');
                    }
                }.bind(this)
            );
            uiRegistry.async('checkout.iosc.ajax')(
                function (ajax) {

                    if (customer.isLoggedIn()) {
                        ajax.addMethod('params', 'address', this.paramsHandler.bind(this));
                    }

                    ajax.addMethod('params', 'billingAddress', this.paramsHandler.bind(this));

                    domObserver.get("#iosc-billing-container [name='billing_address_id']", function (elem) {
                        jQuery(elem).change(ajax.update.bind(ajax));
                    });
                    domObserver.get(
                        "#iosc-billing-container select[name$='_id'],#iosc-billing-container input[name='postcode']",
                        function (elem) {
                            jQuery(elem).change(ajax.update.bind(ajax));
                        }
                    );


                }.bind(this)
            );

            additionalValidators.registerValidator(this.getValidator());
        },

        onDomReady: function () {
            fieldsObj.applyCssClassnames(
                this.dataScopePrefix,
                '#iosc-billing-container fieldset#billing-new-address-form > div, #iosc-billing-container fieldset#billing-new-address-form > fieldset'
            );
            uiRegistry.async('checkout.iosc.shippingfields')(
                function (shippingfields) {
                    if (shippingfields.domReady()) {
                        this.prepDom();
                    } else {
                        shippingfields.domReady.subscribe(
                            this.prepDom.bind(this),
                            true,
                            'change'
                        );
                    }
                }.bind(this)
            );

        },

        prepDom: function () {
            var billingAreaDom = jQuery('#iosc-billing-container').get(0);
            if (billingAreaDom) {
                if (quote.isVirtual()) {
                    if (quote.isVirtual()) {
                        uiRegistry.async("checkout.steps.billing-step.payment.customer-email")(
                            function (customerEmail) {
                                domObserver.get('li#payment form.form-login', function (elem) {
                                    jQuery('#co-payment-form br').hide();
                                    var billingContainer = jQuery('#iosc-billing-container');
                                    var emailElem = jQuery(elem);
                                    var target, cloneTarget;

                                    target = jQuery("#checkout-step-shipping");
                                    cloneTarget = jQuery('#shipping');
                                    var elemToCreate = document.createElement(cloneTarget.get(0).tagName);

                                    target = jQuery(elemToCreate).attr("id", "iosc-billing");
                                    target.append(billingContainer);
                                    cloneTarget.before(target);
                                    var billingAddressForm = jQuery('#iosc-billing .billing-address-form');
                                    billingAddressForm.before(emailElem);
                                });
                            }.bind(this)
                        );
                    }
                } else {
                    var billingContainer = jQuery('#iosc-billing-container');
                    var target =  jQuery("#checkout-step-shipping");
                    target.append(billingContainer);
                }

                this.shippingDomReady(true);
            }
        },

        /**
         *
         */
        showBillingAddress: function () {

            var billingAddress, addressData, newBillingAddress;

            if (this.isUseBillingAddress()) {
                this.isAddressSameAsShipping(false);
                this.isAddressDetailsVisible(true);
                this.isBillingAddressDetailsVisible(true);
            } else {
                this.isAddressSameAsShipping(true);
                this.isAddressDetailsVisible(false);
                this.isBillingAddressDetailsVisible(false);
            }

            this.useShippingAddress();

            if (!this.isUseBillingAddress()) {
                addressData = this.source.get(this.dataScopePrefix);
                addressData.save_in_address_book = this.saveInAddressBook() ?  1 : 0 ;
                newBillingAddress = createBillingAddress(addressData);
                selectBillingAddress(newBillingAddress);

                if (typeof this.updateAddresses === "function") {
                    this.updateAddresses();
                } else {
                    setBillingAddressAction(globalMessageList);
                }
            }

            if (this.selectedAddress() && this.selectedAddress().getAddressInline() != newAddressOption.getAddressInline()) {
                if (typeof this.updateAddresses === "function") {
                    this.updateAddresses();
                }
            }

            uiRegistry.async("checkout.iosc.ajax")(
                function (ajax) {
                    ajax.update();
                }
            );

            return true;
        },

        paramsHandler: function () {

            if (!this.dataScopePrefix) {
                return false;
            }

            var billingAddress, addressData, newBillingAddress;
            if (!this.isUseBillingAddress() && !quote.isVirtual()) {
                if (customer.isLoggedIn()) {
                    newBillingAddress = _.clone(quote.shippingAddress());
                } else {
                    addressData = checkoutData.getShippingAddressFromData();
                    newBillingAddress = createBillingAddress(addressData);
                }
            } else {
                if (customer.isLoggedIn()) {
                    if (this.selectedAddress() && this.selectedAddress().getAddressInline() != newAddressOption.getAddressInline()) {
                        newBillingAddress = this.selectedAddress();
                    } else {
                        addressData = this.source.get(this.dataScopePrefix);
                        newBillingAddress = createBillingAddress(addressData);
                    }
                } else {
                    addressData = this.source.get(this.dataScopePrefix);
                    newBillingAddress = createBillingAddress(addressData);
                }
            }

            if (typeof addressData !== "undefined" && addressData !== null && typeof addressData.extension_attributes !== "undefined") {
                newBillingAddress.extension_attributes = addressData.extension_attributes;
            }

            if (customer.isLoggedIn()) {
                if (!this.isUseBillingAddress()) {
                    newBillingAddress.save_in_address_book = 0;
                } else {
                    newBillingAddress.save_in_address_book = this.saveInAddressBook() ? "1" : "0";
                }
            }

            selectBillingAddress(newBillingAddress);

            return quote.billingAddress();
        },

        getValidator: function () {
            return {
                validate: this.validationHandler.bind(this)
                };
        },

        validationHandler: function () {

            if (!this.isUseBillingAddress() && !quote.isVirtual()) {
                return true;
            }

            var isValid = false;

            if (this.selectedAddress() && this.selectedAddress().getAddressInline() != newAddressOption.getAddressInline()) {
                isValid = true;
            } else {
                this.source.set('params.invalid', false);
                this.source.trigger(this.dataScopePrefix + '.data.validate');

                if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                    this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                }

                if (this.source.get(this.dataScopePrefix + ".extension_attributes")) {
                    this.source.trigger(this.dataScopePrefix + ".extension_attributes.data.validate");
                }

                if (!this.source.get('params.invalid')) {
                    isValid = true;
                }
            }

            if (!isValid && _.isFunction(this.formObjects.focusInvalid)) {
                this.formObjects.focusInvalid();
            }

            return isValid;
        }


    });

});

