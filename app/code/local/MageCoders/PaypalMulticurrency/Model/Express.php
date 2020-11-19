<?php

class MageCoders_PaypalMulticurrency_Model_Express extends Mage_Paypal_Model_Express{

	

	protected $_helper; 

	

	

	public function __construct(){

		parent::__construct();

		$this->_helper =  Mage::helper('paypalmulticurrency');

	}

	





	public function canUseForCurrency($currencyCode)

    {

		return true; //allow for all currencies

    }

	

	/**

     * Capture payment

     *

     * @param Mage_Sales_Model_Order_Payment $payment

     * @param float $amount

     * @return Mage_Paypal_Model_Express

     */

    public function capture(Varien_Object $payment, $amount)

    {

		

		

		if(!$payment){

			return parent::capture($payment,$amount);

		}

		

		$authorizationTransaction = $payment->getAuthorizationTransaction();

        $authorizationPeriod = abs(intval($this->getConfigData('authorization_honor_period')));

        $maxAuthorizationNumber = abs(intval($this->getConfigData('child_authorization_number')));

        $order = $payment->getOrder();

        $isAuthorizationCreated = false;

		

		

		$total = $order->getGrandTotal();

		$currency = $this->_helper->getCurrencyCode($order);

		$amount = $this->_helper->convertCurrency($total);



        if ($payment->getAdditionalInformation($this->_isOrderPaymentActionKey)) {

            $voided = false;

            if (!$authorizationTransaction->getIsClosed()

                && $this->_isTransactionExpired($authorizationTransaction, $authorizationPeriod)

            ) {

                //Save payment state and configure payment object for voiding

                $isCaptureFinal = $payment->getShouldCloseParentTransaction();

                $captureTrxId = $payment->getTransactionId();

                $payment->setShouldCloseParentTransaction(false);

                $payment->setParentTransactionId($authorizationTransaction->getTxnId());

                $payment->unsTransactionId();

                $payment->setVoidOnlyAuthorization(true);

                $payment->void(new Varien_Object());



                //Revert payment state after voiding

                $payment->unsAuthorizationTransaction();

                $payment->unsTransactionId();

                $payment->setShouldCloseParentTransaction($isCaptureFinal);

                $voided = true;

            }



            if ($authorizationTransaction->getIsClosed() || $voided) {

                if ($payment->getAdditionalInformation($this->_authorizationCountKey) > $maxAuthorizationNumber - 1) {

                    Mage::throwException(Mage::helper('paypal')->__('The maximum number of child authorizations is reached.'));

                }

                $api = $this->_callDoAuthorize(

                    $amount,

                    $payment,

                    $authorizationTransaction->getParentTxnId()

                );



                //Adding authorization transaction

                $this->_pro->importPaymentInfo($api, $payment);

                $payment->setTransactionId($api->getTransactionId());

                $payment->setParentTransactionId($authorizationTransaction->getParentTxnId());

                $payment->setIsTransactionClosed(false);



                //$formatedPrice = $order->getBaseCurrency()->formatTxt($amount);

				$formatedPrice = $order->getOrderCurrency()->formatTxt($amount);



                if ($payment->getIsTransactionPending()) {

                    $message = Mage::helper('paypal')->__('Authorizing amount of %s is pending approval on gateway.', $formatedPrice);

                } else {

                    $message = Mage::helper('paypal')->__('Authorized amount of %s.', $formatedPrice);

                }



                $transaction = $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH, null,

                    true, $message

                );



                $payment->setParentTransactionId($api->getTransactionId());

                $isAuthorizationCreated = true;

            }

            //close order transaction if needed

            if ($payment->getShouldCloseParentTransaction()) {

                $orderTransaction = $payment->lookupTransaction(

                    false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER

                );



                if ($orderTransaction) {

                    $orderTransaction->setIsClosed(true);

                    $order->addRelatedObject($orderTransaction);

                }

            }

        }



        if (false === $this->_pro->capture($payment, $amount)) {

            $this->_placeOrder($payment, $amount);

        }



        if ($isAuthorizationCreated && isset($transaction)) {

            $transaction->setIsClosed(true);

        }



        return $this;

	}

	

	

	/**

     * Place an order with authorization or capture action

     *

     * @param Mage_Sales_Model_Order_Payment $payment

     * @param float $amount

     * @return Mage_Paypal_Model_Express

     */

    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)

    {

	

	

        $order = $payment->getOrder();

		$total = $order->getGrandTotal();

		

		$currency = $this->_helper->getCurrencyCode($order);

		$amount = $this->_helper->convertCurrency($total);

	

		$paypalCart = Mage::getModel('paypal/cart', array($order));

		

		$totals = $paypalCart->getTotals();

		$items = $paypalCart->getItems();

		

		$discount = 0;		

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

		

		if($newTotal != $amount){

			$amount = $newTotal;

		}



	

	

	

        // prepare api call

        $token = $payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);

        $api = $this->_pro->getApi()

            ->setToken($token)

            ->setPayerId($payment->

                getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))

            ->setAmount($amount)

            ->setPaymentAction($this->_pro->getConfig()->paymentAction)

            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))

            ->setInvNum($order->getIncrementId())

            //->setCurrencyCode($order->getBaseCurrencyCode())

			->setCurrencyCode($currency)

            ->setPaypalCart($paypalCart)

            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled);



        // call api and get details from it

        $api->callDoExpressCheckoutPayment();

		



        $this->_importToPayment($api, $payment);

        return $this;

    }



	

	

}