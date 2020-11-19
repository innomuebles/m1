<?php
    class MST_Storepickup_Model_Observer extends Varien_Object
    {				

       public function saveShippingMethod($evt){
		$request = $evt->getRequest();
		$quote = $evt->getQuote();
        $pickupId = $request->getParam('mst_store_id');
		$pickupId = isset($pickupId) ? (int)$pickupId : 0;
        $menthod = $request->getParam('shipping_method');
        $date_pickup = $request->getParam('pickup_shipping_data');
		$shipping_time_pickup = $request->getParam('shipping_time_pickup',false);
        if($menthod == 'storepickup_storepickup' && $pickupId > 0)
        {
          $data = array('pickup_id' => $pickupId,'time_pickup'=>$date_pickup . ' '. $shipping_time_pickup); 
        }
		else
        {
            $data = array();
        }
		Mage::getSingleton('checkout/session')->setPickupData($data);
	}
	public function saveOrderAfter($evt){
		$order = $evt->getOrder();
		$quote = $evt->getQuote();
		$pickupData = Mage::getSingleton('checkout/session')->getPickupData();
		if(count($pickupData)){
			$data = $pickupData;
            $data['pickup_order_id'] = $order->getId();
			$data['pickup_id'] = $pickupData['pickup_id'];
			$storePickupOrder = Mage::getModel('storepickup/pickuporder');
			$storePickupOrder->setData($data);
			$storePickupOrder->save();
		}
	}
}