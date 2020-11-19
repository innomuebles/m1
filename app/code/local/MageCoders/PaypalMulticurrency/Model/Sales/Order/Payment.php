<?php
/**
 * @author   MageCoders
 * @package    MageCoders_PaypalMulticurrency
 */
class MageCoders_PaypalMulticurrency_Model_Sales_Order_Payment extends Mage_Sales_Model_Order_Payment{

 /**
     * Decide whether authorization transaction may close (if the amount to capture will cover entire order)
     * @param float $amountToCapture
     * @return bool
     */
	protected $_helper; 
	
	protected function _construct()
    {
		parent::_construct();
		$this->_helper = Mage::helper('paypalmulticurrency');
	}
	
	protected function _isCaptureFinal($amountToCapture)
    {
		$defaultBase  = false;


        $amountToCapture = $this->_formatAmount($amountToCapture, true);
        
		$baseCurrency = $this->getOrder()->getBaseCurrencyCode(); 
		if(!$this->_helper->isCurrencyCodeSupported($baseCurrency)){
			$baseCurrency = 'USD';
			$defaultBase = true;
		}
		
		$orderCurrency = $this->getOrder()->getOrderCurrencyCode();
		$orderGrandTotal = $this->_formatAmount($this->getOrder()->getGrandTotal(), true);
	
		if(!$this->_helper->isCurrencyCodeSupported($orderCurrency)){
			$orderGrandTotal = $this->_helper->convertCurrency($orderGrandTotal,$baseCurrency,$orderCurrency);
		}
		
		if($defaultBase){
			$orderGrandTotal = number_format($orderGrandTotal,2,'.','');
		}
		$captureAmount = $this->_formatAmount($this->getBaseAmountPaid(), true) + $amountToCapture;
		
		$this->_helper->debug('capture:'.$captureAmount.' --- OrderTotal:'.$orderGrandTotal);
		
		
		
     	if ($orderGrandTotal == $this->_formatAmount($this->getBaseAmountPaid(), true) + $amountToCapture) {
	        if (false !== $this->getShouldCloseParentTransaction()) {
                $this->setShouldCloseParentTransaction(true);
            }
            return true;
        }
        return false;
    }	

	protected function _isSameCurrency()
    {
	
		$currency = $this->getOrder()->getOrderCurrencyCode();
		if(!$this->_helper->isCurrencyCodeSupported($currency)){
			$currency = $this->getBaseCurrencyCode();
		}
	   return !$this->getCurrencyCode() || $this->getCurrencyCode() == $currency;
    }
	
	
	protected function _formatPrice($amount, $currency = null)
    {
		$this->_helper = Mage::helper('paypalmulticurrency');
	
		$currency = $this->getOrder()->getOrderCurrencyCode();
		if(!$this->_helper->isCurrencyCodeSupported($currency)){
			$currency = $this->_helper->getCurrencyCode($this->getOrder());
		}
		return parent::_formatPrice($amount, $currency);	
        
    }


}