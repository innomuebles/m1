<?php  

class Payu_PayuCheckout_Model_Shared extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'payucheckout_shared';

    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'payucheckout/shared_form';
    protected $_paymentMethod = 'shared';
     
    
    protected $_order;


    public function cleanString($string) {
        
        $string_step1 = strip_tags($string);
        $string_step2 = nl2br($string_step1);
        $string_step3 = str_replace("<br />","<br>",$string_step2);
        $cleaned_string = str_replace("\""," inch",$string_step3);        
        return $cleaned_string;
    }


    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    
    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }
	

    public function getCustomerId()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/customer_id');
    }
	
    public function getAccepteCurrency()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/currency');
    }
	
	
	
	
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('payucheckout/shared/redirect');
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
	
	    $billing = $this->getOrder()->getBillingAddress();
        $coFields = array();
        $items = $this->getQuote()->getAllItems();
		
		if ($items) {
            $i = 1;
            foreach($items as $item){
                if ($item->getParentItem()) {
                   continue;
                }        
                $coFields['c_prod_'.$i]            = $this->cleanString($item->getSku());
                $coFields['c_name_'.$i]            = $this->cleanString($item->getName());
                $coFields['c_description_'.$i]     = $this->cleanString($item->getDescription());
                $coFields['c_price_'.$i]           = number_format($item->getPrice(), 2, '.', '');
            $i++;
            }
        }
        
        $request = '';
        foreach ($coFields as $k=>$v) {
            $request .= '<' . $k . '>' . $v . '</' . $k . '>';
        }
		
		
		$key=Mage::getStoreConfig('payment/payucheckout_shared/key');
		$salt=Mage::getStoreConfig('payment/payucheckout_shared/salt');
		$debug_mode=Mage::getStoreConfig('payment/payucheckout_shared/debug_mode');
	
	    $orderId = $this->getOrder()->getRealOrderId(); 
	   // $txnid = $orderId+370000; 
	    $txnid = $orderId; 
		
		$coFields['key']          = $key;
		$coFields['txnid']        =  $txnid;
		
		$coFields['amount']       =  number_format($this->getOrder()->getBaseGrandTotal(),0,'','');  
		$coFields['productinfo']  = 'Prpduct Information';  
		$coFields['firstname']    = $billing->getFirstname();
		$coFields['Lastname']     = $billing->getLastname();
		$coFields['City']         = $billing->getCity();
        $coFields['State']        = $billing->getRegion();
		$coFields['Country']      = $billing->getCountry();
        $coFields['Zipcode']      = $billing->getPostcode();
		$coFields['email']        = $this->getOrder()->getCustomerEmail();
        $coFields['phone']        = $billing->getTelephone();
		 
		$coFields['surl']         =  Mage::getBaseUrl().'payucheckout/shared/success/';  
		$coFields['furl']         =  Mage::getBaseUrl().'payucheckout/shared/failure/';
		$coFields['curl']         =  Mage::getBaseUrl().'payucheckout/shared/canceled/id/'.$this->getOrder()->getRealOrderId();
		
		

		
		$coFields['Pg']           =  'CC';
		$debugId='';
		
        if ($debug_mode==1) {
		
		$requestInfo= $key.'|'.$coFields['txnid'].'|'.$coFields['amount'].'|'.
$coFields['productinfo'].'|'.$coFields['firstname'].'|'.$coFields['email'].'|'.$debugId.'||||||||||'.$salt;
            $debug = Mage::getModel('payucheckout/api_debug')
                ->setRequestBody($requestInfo)
                ->save();
					
			$debugId = $debug->getId();	
			
			$coFields['udf1']=$debugId;
			$coFields['Hash']    =   hash('sha512', $key.'|'.$coFields['txnid'].'|'.$coFields['amount'].'|'.
$coFields['productinfo'].'|'.$coFields['firstname'].'|'.$coFields['email'].'|'.$debugId.'||||||||||'.$salt);
        }
else
{
 $coFields['Hash']         =   hash('sha512', $key.'|'.$coFields['txnid'].'|'.$coFields['amount'].'|'.
 $coFields['productinfo'].'|'.$coFields['firstname'].'|'.$coFields['email'].'|||||||||||'.$salt);
}
        return $coFields;
    }

    /**
     * Get url of Payu payment
     *
     * @return string
     */
    public function getPayuCheckoutSharedUrl()
    {
        $mode=Mage::getStoreConfig('payment/payucheckout_shared/demo_mode');
		
		$url='https://www.innomuebles.com/index.php';
		
		if($mode=='')
		{
		  $url='https://www.innomuebles.com/bncr/index.php';
		}
		 
         return $url;
    }
       

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    public function parseResponse()
    {       

            return true;
    
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }
	
	
	public function getResponseOperation($response)
	{

	   $order = Mage::getModel('sales/order');
	   $debug_mode=Mage::getStoreConfig('payment/payucheckout_shared/debug_mode');
	   $key=Mage::getStoreConfig('payment/payucheckout_shared/key');
	   $salt=Mage::getStoreConfig('payment/payucheckout_shared/salt');
	    if(isset($_POST['status']))
		{
		   $txnid=$_POST['txnid'];
		  // $orderid=$txnid-370000;
		   $orderid=$txnid;
		   if($_POST['status']=='success')
			{
				$status='success';
				$order->loadByIncrementId($orderid);

				$billing = $order->getBillingAddress();
				$amount      =  $_POST['amount'];
				$productinfo = 'Informacion de Compra';
				$firstname   = $billing->getFirstname();
				$email       =  $order->getCustomerEmail();
				$invoice = $order->prepareInvoice();
		        $invoice->register()->capture();
		        $order->addRelatedObject($invoice);
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
				$this->updateInventory($orderid);


				$order->save();
				$order->sendNewOrderEmail();

				$cart = Mage::getSingleton('checkout/cart');
				foreach( Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection() as $item ){
				$cart->removeItem( $item->getId() );
				}
				$cart->save();


			   }

		   if($_POST['status']=='failure')
		   {
		       $order->loadByIncrementId($orderid);
		       $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
		       // Invento updated
			   $this->updateInventory($orderid);

			   $order->cancel()->save();



		   }
		   if($_POST['status']=='pending')
		   {
		       $order->loadByIncrementId($orderid);
		       $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
		       // Invento updated  
		       $this->updateInventory($orderid);
			   $order->cancel()->save();

		   }

		}
        else
		{

		   $order->loadByIncrementId($_POST['id']);
		   $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
		  // Invento updated 
		   $order_id=$response['id'];
		   $this->updateInventory($order_id);

		   $order->cancel()->save();

		}
	}


    public function updateInventory($order_id)
    {

        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = $order->getAllItems();
		foreach ($items as $itemId => $item)
		{
		   $ordered_quantity = $item->getQtyToInvoice();
		   $sku=$item->getSku();
		   $product = Mage::getModel('catalog/product')->load($item->getProductId());
		   $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId())->getQty();

		   $updated_inventory=$qtyStock+ $ordered_quantity;

		   $stockData = $product->getStockItem();
		   $stockData->setData('qty',$updated_inventory);
		   $stockData->save();

	   }
    }
}