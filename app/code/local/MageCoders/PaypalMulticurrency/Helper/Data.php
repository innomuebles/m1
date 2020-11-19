<?php

/**

 * @author   MageCoders

 * @package    MageCoders_PaypalMulticurrency  

 */ 

class MageCoders_PaypalMulticurrency_Helper_Data extends Mage_Core_Helper_Abstract{

 

	public function getCurrencyCode($obj){ 

		if($obj instanceof Mage_Sales_Model_Order){

			$code = $obj->getOrderCurrencyCode();

		}elseif($obj instanceof Mage_Sales_Model_Quote){

			$code = $obj->getQuoteCurrencyCode();

		}

		$currency = $this->getPaypalSupportedCurrency($code);

		if(!$currency){

			$currency = $this->switchCurrency();

		}

		

		if($currency){

			// unset old values

			Mage::unregister('order_currency');

			Mage::unregister('paypal_currency');		



			//set data in registry		

			Mage::register('order_currency',$code);

			Mage::register('paypal_currency',$currency);		

		}

		return $currency;

	}

	

	

	public function convertAny($price,$from,$to){

	

		$directory = Mage::getModel('directory/currency')->getResource();

		$rate = $directory->getAnyRate($from,$to);

		$newPrice = (float)($price*$rate);

		return $newPrice;

	}	

	

	public function convertCurrency($price,$from = null,$to = null){

		



		

		if(!$from){

			$from = Mage::registry('order_currency');

		}

		if(empty($from)){ return $price; }

		if(empty($to)){

			$to =  Mage::registry('paypal_currency');

		}

		

		//return if both currency same.

		if($from == $to){

			return $price;

		}

		

		$directory = Mage::getModel('directory/currency')->getResource();

		$rate = $directory->getAnyRate($from,$to);

		

		if($this->isCurrencyCodeSupported($from)){

			$newPrice = (float)($price/$rate);

		}else{

			$newPrice = (float)($price*$rate);

		}

		$newPrice = $this->round_down($newPrice,3);

		

		return $newPrice;

	}

	

	function round_down($number, $precision = 2)

	{

		$fig = (int) str_pad('1', $precision, '0');

		return (floor($number * $fig) / $fig);

	}

	

	

	public function isCurrencyCodeSupported($currency){



		$allowed_currencies = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN',

        'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD', 'TWD', 'THB','BRL','RUB','TRY');

		if(in_array($currency,$allowed_currencies)){

			return true;

		}

		return false;

	}

	

	

	public function getPaypalSupportedCurrency($currency){

		

		$ord_currency = $currency;

		$base_currency = Mage::app()->getBaseCurrencyCode(); 

		

		if($this->isCurrencyCodeSupported($ord_currency)){

			return $ord_currency;

		}elseif($this->isCurrencyCodeSupported($base_currency)){

			return $base_currency;

		}else{

			return false;

		}

		

	}

	

	

	protected function switchCurrency(){

		

		$baseCurrencyCode = Mage::app()->getBaseCurrencyCode(); 

		

		$allowedCurrencies = Mage::getModel('directory/currency')

						->getConfigAllowCurrencies();   

						

		if($this->isCurrencyCodeSupported($baseCurrencyCode)){

			return $baseCurrencyCode;

		}						

		$currencies = array_keys($allowedCurrencies);

		if(in_array('USD',$currencies)){

			$currency = 'USD';

		}else{

			foreach($currencies as $cr){

				if($this->isCurrencyCodeSupported($cr)){

					$currency = $cr;

					break;

				}

			}

		}

		return $currency;

		

	}

	



	public function debug($message){

		Mage::log($message,null,'pmc.log');

	}

	

	public function isActive(){

		return true;

	}

		

}

