<?php

class MageCoders_PaypalMulticurrency_Model_Express_Checkout extends Mage_Paypal_Model_Express_Checkout{

	 

	

	protected $_helper;

	

	public function __construct($params){

		parent::__construct($params);

		$this->_helper = Mage::helper('paypalmulticurrency');

	}

	

	public function start($returnUrl, $cancelUrl, $button = null)

    {  

	

		$version = Mage::getVersion();

 	    if(version_compare($version,'1.9.0.0')==0){

			return $this->_start190($returnUrl, $cancelUrl,$button);

		}elseif(version_compare($version,'1.9.1.0')>=0){

			return $this->_start191($returnUrl, $cancelUrl,$button);

		}

		

		$this->_quote->collectTotals();



        if (!$this->_quote->getGrandTotal() && !$this->_quote->hasNominalItems()) {

            Mage::throwException(Mage::helper('paypal')->__('PayPal does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.'));

        }



        $this->_quote->reserveOrderId()->save();

        // prepare API

        $this->_getApi();

        $this->_api->setAmount($this->_quote->getGrandTotal())

            ->setCurrencyCode($this->_quote->getQuoteCurrencyCode())

            ->setInvNum($this->_quote->getReservedOrderId())

            ->setReturnUrl($returnUrl)

            ->setCancelUrl($cancelUrl)

            ->setSolutionType($this->_config->solutionType)

            ->setPaymentAction($this->_config->paymentAction);

			

			

        if ($this->_giropayUrls) {

            list($successUrl, $cancelUrl, $pendingUrl) = $this->_giropayUrls;

            $this->_api->addData(array(

                'giropay_cancel_url' => $cancelUrl,

                'giropay_success_url' => $successUrl,

                'giropay_bank_txn_pending_url' => $pendingUrl,

            ));

        }



        $this->_setBillingAgreementRequest();

		

		$version = Mage::getVersion();

       if(version_compare($version,'1.7.0.0')>=0){

			if ($this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_ALL) {

				$this->_api->setRequireBillingAddress(1);

			}		

		}



        // supress or export shipping address

        if ($this->_quote->getIsVirtual()) {

            if ($this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_VIRTUAL) {

                $this->_api->setRequireBillingAddress(1);

            }

            $this->_api->setSuppressShipping(true);

        } else {

            $address = $this->_quote->getShippingAddress();

            $isOverriden = 0;

            if (true === $address->validate()) {

                $isOverriden = 1;

                $this->_api->setAddress($address);

            }

            $this->_quote->getPayment()->setAdditionalInformation(

                self::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN, $isOverriden

            );

            $this->_quote->getPayment()->save();

        }

		

        // add line items

        $paypalCart = Mage::getModel('paypal/cart', array($this->_quote));

        $this->_api->setPaypalCart($paypalCart)

            ->setIsLineItemsEnabled($this->_config->lineItemsEnabled)

        ;

		

        // add shipping options if needed and line items are available

        if ($this->_config->lineItemsEnabled && $this->_config->transferShippingOptions && $paypalCart->getItems()) {

            if (!$this->_quote->getIsVirtual() && !$this->_quote->hasNominalItems()) {

                if ($options = $this->_prepareShippingOptions($address, true)) {

                    $this->_api->setShippingOptionsCallbackUrl(

                        Mage::getUrl('*/*/shippingOptionsCallback', array('quote_id' => $this->_quote->getId()))

                    )->setShippingOptions($options);

                }

            }

        }





        // add recurring payment profiles information

        if ($profiles = $this->_quote->prepareRecurringPaymentProfiles()) {

            foreach ($profiles as $profile) {

                $profile->setMethodCode(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);

                if (!$profile->isValid()) {

                    Mage::throwException($profile->getValidationErrors(true, true));

                }

            }

            $this->_api->addRecurringPaymentProfiles($profiles);

        }



        $this->_config->exportExpressCheckoutStyleSettings($this->_api);





        // call API and redirect with token

        $this->_api->callSetExpressCheckout();

        $token = $this->_api->getToken();

        $this->_redirectUrl = $this->_config->getExpressCheckoutStartUrl($token);



        $this->_quote->getPayment()->unsAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);

        $this->_quote->getPayment()->save();

        return $token;

	}

	

	

	protected function _start190($returnUrl, $cancelUrl, $button = null){

	

	

        $this->_quote->collectTotals();



        if (!$this->_quote->getGrandTotal() && !$this->_quote->hasNominalItems()) {

            Mage::throwException(Mage::helper('paypal')->__('PayPal does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.'));

        }



        $this->_quote->reserveOrderId()->save();

        // prepare API

        $this->_getApi();

        $solutionType = $this->_config->getMerchantCountry() == 'DE'

            ? Mage_Paypal_Model_Config::EC_SOLUTION_TYPE_MARK : $this->_config->solutionType;

        $this->_api->setAmount($this->_quote->getGrandTotal())

            ->setCurrencyCode($this->_quote->getQuoteCurrencyCode())

            ->setInvNum($this->_quote->getReservedOrderId())

            ->setReturnUrl($returnUrl)

            ->setCancelUrl($cancelUrl)

            ->setSolutionType($solutionType)

            ->setPaymentAction($this->_config->paymentAction);



        if ($this->_giropayUrls) {

            list($successUrl, $cancelUrl, $pendingUrl) = $this->_giropayUrls;

            $this->_api->addData(array(

                'giropay_cancel_url' => $cancelUrl,

                'giropay_success_url' => $successUrl,

                'giropay_bank_txn_pending_url' => $pendingUrl,

            ));

        }



        if ($this->_isBml) {

            $this->_api->setFundingSource('BML');

        }



        $this->_setBillingAgreementRequest();



        if ($this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_ALL) {

            $this->_api->setRequireBillingAddress(1);

        }



        // supress or export shipping address

        if ($this->_quote->getIsVirtual()) {

            if ($this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_VIRTUAL) {

                $this->_api->setRequireBillingAddress(1);

            }

            $this->_api->setSuppressShipping(true);

        } else {

            $address = $this->_quote->getShippingAddress();

            $isOverriden = 0;

            if (true === $address->validate()) {

                $isOverriden = 1;

                $this->_api->setAddress($address);

            }

            $this->_quote->getPayment()->setAdditionalInformation(

                self::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN, $isOverriden

            );

            $this->_quote->getPayment()->save();

        }



        // add line items

        $paypalCart = Mage::getModel('paypal/cart', array($this->_quote));

        $this->_api->setPaypalCart($paypalCart)

            ->setIsLineItemsEnabled($this->_config->lineItemsEnabled)

        ;



        // add shipping options if needed and line items are available

        if ($this->_config->lineItemsEnabled && $this->_config->transferShippingOptions && $paypalCart->getItems()) {

            if (!$this->_quote->getIsVirtual() && !$this->_quote->hasNominalItems()) {

                if ($options = $this->_prepareShippingOptions($address, true)) {

                    $this->_api->setShippingOptionsCallbackUrl(

                        Mage::getUrl('*/*/shippingOptionsCallback', array('quote_id' => $this->_quote->getId()))

                    )->setShippingOptions($options);

                }

            }

        }



        // add recurring payment profiles information

        if ($profiles = $this->_quote->prepareRecurringPaymentProfiles()) {

            foreach ($profiles as $profile) {

                $profile->setMethodCode(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);

                if (!$profile->isValid()) {

                    Mage::throwException($profile->getValidationErrors(true, true));

                }

            }

            $this->_api->addRecurringPaymentProfiles($profiles);

        }



        $this->_config->exportExpressCheckoutStyleSettings($this->_api);



        // call API and redirect with token

        $this->_api->callSetExpressCheckout();

        $token = $this->_api->getToken();

        $this->_redirectUrl = $button ? $this->_config->getExpressCheckoutStartUrl($token)

            : $this->_config->getPayPalBasicStartUrl($token);



        $this->_quote->getPayment()->unsAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);



        // Set flag that we came from Express Checkout button

        if (!empty($button)) {

            $this->_quote->getPayment()->setAdditionalInformation(self::PAYMENT_INFO_BUTTON, 1);

        } elseif ($this->_quote->getPayment()->hasAdditionalInformation(self::PAYMENT_INFO_BUTTON)) {

            $this->_quote->getPayment()->unsAdditionalInformation(self::PAYMENT_INFO_BUTTON);

        }



        $this->_quote->getPayment()->save();

        return $token;

    

	

	}

	

	

	 /**

     * Checkout with PayPal image URL getter

     * Spares API calls of getting "pal" variable, by putting it into cache per store view

     * @return string

     */

    public function getCheckoutShortcutImageUrl()

    {

        // get "pal" thing from cache or lookup it via API

        $pal = null;

        if ($this->_config->areButtonsDynamic()) {

            $cacheId = self::PAL_CACHE_ID . Mage::app()->getStore()->getId();

            $pal = Mage::app()->loadCache($cacheId);

            if (-1 == $pal) {

                $pal = null;

            } elseif (!$pal) {

                $pal = null;

                $this->_getApi();

                try {

                    $this->_api->callGetPalDetails();

                    $pal = $this->_api->getPal();

                    Mage::app()->saveCache($pal, $cacheId, array(Mage_Core_Model_Config::CACHE_TAG));

                } catch (Exception $e) {

                    Mage::app()->saveCache(-1, $cacheId, array(Mage_Core_Model_Config::CACHE_TAG));

                    Mage::logException($e);

                }

            }

        }



        return $this->_config->getExpressCheckoutShortcutImageUrl(

            Mage::app()->getLocale()->getLocaleCode(),

            $this->_quote->getGrandTotal(),

            $pal

        );

    }

	

	

	protected function _start191($returnUrl, $cancelUrl, $button = null)

    {

        $this->_quote->collectTotals();



        if (!$this->_quote->getGrandTotal() && !$this->_quote->hasNominalItems()) {

            Mage::throwException(Mage::helper('paypal')->__('PayPal does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.'));

        }



        $this->_quote->reserveOrderId()->save();

		

		

		$paypalCart = Mage::getModel('paypal/cart', array($this->_quote));

		$currency = $this->_helper->getCurrencyCode($this->_quote);

		

		$grandTotal = $this->_helper->convertCurrency($this->_quote->getGrandTotal());

		

		$totals = $paypalCart->getTotals();

		$items = $paypalCart->getItems();

		

		$discount = 0;	
        $newTotal = 0;	

		foreach($totals as $code=>$value){

			if($code == 'discount'){

				$discount += $value;				

			}else{

				$newTotal += $value;				

			}

        }

		

		if($discount>0){

			$newTotal = (float)($newTotal - $discount);

		}

		

		

		if($grandTotal != $newTotal){

			$grandTotal = $newTotal;

		}

		 

        // prepare API

        $this->_getApi();

        $solutionType = $this->_config->getMerchantCountry() == 'DE'

            ? Mage_Paypal_Model_Config::EC_SOLUTION_TYPE_MARK : $this->_config->solutionType;

        $this->_api

			->setAmount($grandTotal)

			->setCurrencyCode($currency)

            ->setInvNum($this->_quote->getReservedOrderId())

            ->setReturnUrl($returnUrl)

            ->setCancelUrl($cancelUrl)

            ->setSolutionType($solutionType)

            ->setPaymentAction($this->_config->paymentAction);



        if ($this->_giropayUrls) {

            list($successUrl, $cancelUrl, $pendingUrl) = $this->_giropayUrls;

            $this->_api->addData(array(

                'giropay_cancel_url' => $cancelUrl,

                'giropay_success_url' => $successUrl,

                'giropay_bank_txn_pending_url' => $pendingUrl,

            ));

        }



        if ($this->_isBml) {

            $this->_api->setFundingSource('BML');

        }



        $this->_setBillingAgreementRequest();



        if ($this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_ALL) {

            $this->_api->setRequireBillingAddress(1);

        }



        // supress or export shipping address

        if ($this->_quote->getIsVirtual()) {

            if ($this->_config->requireBillingAddress == Mage_Paypal_Model_Config::REQUIRE_BILLING_ADDRESS_VIRTUAL) {

                $this->_api->setRequireBillingAddress(1);

            }

            $this->_api->setSuppressShipping(true);

        } else {

            $address = $this->_quote->getShippingAddress();

            $isOverriden = 0;

            if (true === $address->validate()) {

                $isOverriden = 1;

                $this->_api->setAddress($address);

            }

            $this->_quote->getPayment()->setAdditionalInformation(

                self::PAYMENT_INFO_TRANSPORT_SHIPPING_OVERRIDEN, $isOverriden

            );

            $this->_quote->getPayment()->save();

        }



        // add line items

        //$paypalCart = Mage::getModel('paypal/cart', array($this->_quote));

        $this->_api->setPaypalCart($paypalCart)

            ->setIsLineItemsEnabled($this->_config->lineItemsEnabled)

        ;



        // add shipping options if needed and line items are available

        if ($this->_config->lineItemsEnabled && $this->_config->transferShippingOptions && $paypalCart->getItems()) {

            if (!$this->_quote->getIsVirtual() && !$this->_quote->hasNominalItems()) {

                if ($options = $this->_prepareShippingOptions($address, true)) {

                    $this->_api->setShippingOptionsCallbackUrl(

                        Mage::getUrl('*/*/shippingOptionsCallback', array('quote_id' => $this->_quote->getId()))

                    )->setShippingOptions($options);

                }

            }

        }



        // add recurring payment profiles information

        if ($profiles = $this->_quote->prepareRecurringPaymentProfiles()) {

            foreach ($profiles as $profile) {

                $profile->setMethodCode(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);

                if (!$profile->isValid()) {

                    Mage::throwException($profile->getValidationErrors(true, true));

                }

            }

            $this->_api->addRecurringPaymentProfiles($profiles);

        }



        $this->_config->exportExpressCheckoutStyleSettings($this->_api);

		

		





        // call API and redirect with token

        $this->_api->callSetExpressCheckout();

        $token = $this->_api->getToken();

        $this->_redirectUrl = $button ? $this->_config->getExpressCheckoutStartUrl($token)

            : $this->_config->getPayPalBasicStartUrl($token);



        $this->_quote->getPayment()->unsAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);



        // Set flag that we came from Express Checkout button

        if (!empty($button)) {

            $this->_quote->getPayment()->setAdditionalInformation(self::PAYMENT_INFO_BUTTON, 1);

        } elseif ($this->_quote->getPayment()->hasAdditionalInformation(self::PAYMENT_INFO_BUTTON)) {

            $this->_quote->getPayment()->unsAdditionalInformation(self::PAYMENT_INFO_BUTTON);

        }



        $this->_quote->getPayment()->save();

        return $token;

    }



	 /**

     * Make sure addresses will be saved without validation errors

     */

    private function _ignoreAddressValidation()

    {

        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);

        if (!$this->_quote->getIsVirtual()) {

            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);

            if (!$this->_config->requireBillingAddress && !$this->_quote->getBillingAddress()->getEmail()) {

                $this->_quote->getBillingAddress()->setSameAsBilling(1);

            }

        }

    }



    /**

     * Place the order and recurring payment profiles when customer returned from paypal

     * Until this moment all quote data must be valid

     *

     * @param string $token

     * @param string $shippingMethodCode

     */

    public function place($token, $shippingMethodCode = null)

    {

        if ($shippingMethodCode) {

            $this->updateShippingMethod($shippingMethodCode);

        }



        $isNewCustomer = false;

        switch ($this->getCheckoutMethod()) {

            case Mage_Checkout_Model_Type_Onepage::METHOD_GUEST:

                $this->_prepareGuestQuote();

                break;

            case Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER:

                $this->_prepareNewCustomerQuote();

                $isNewCustomer = true;

                break;

            default:

                $this->_prepareCustomerQuote();

                break;

        }

		



        $this->_ignoreAddressValidation();

        $this->_quote->collectTotals();

		

        $service = Mage::getModel('sales/service_quote', $this->_quote);

        $service->submitAll();

		

		

        $this->_quote->save();



        if ($isNewCustomer) {

            try {

                $this->_involveNewCustomer();

            } catch (Exception $e) {

                Mage::logException($e);

            }

        }



        $this->_recurringPaymentProfiles = $service->getRecurringPaymentProfiles();

        // TODO: send recurring profile emails



        $order = $service->getOrder();



		

        if (!$order) {

            return;

        }

        $this->_billingAgreement = $order->getPayment()->getBillingAgreement();



        // commence redirecting to finish payment, if paypal requires it

        if ($order->getPayment()->getAdditionalInformation(

                Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT

        )) {

            $this->_redirectUrl = $this->_config->getExpressCheckoutCompleteUrl($token);

        }



        switch ($order->getState()) {

            // even after placement paypal can disallow to authorize/capture, but will wait until bank transfers money

            case Mage_Sales_Model_Order::STATE_PENDING_PAYMENT:

                // TODO

                break;

            // regular placement, when everything is ok

            case Mage_Sales_Model_Order::STATE_PROCESSING:

            case Mage_Sales_Model_Order::STATE_COMPLETE:

            case Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW:

                $order->sendNewOrderEmail();

                break;

        }

        $this->_order = $order;

    }

}