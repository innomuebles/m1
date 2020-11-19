<?php
class MST_Storepickup_Model_Pickuporder extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/pickuporder');
    }
	function getPickupOrderByOrderId($orderId)
	{
		$pickup = Mage::getModel('storepickup/pickuporder')->getCollection()
				->addFiledToFilter('pickup_order_id',$orderId)
				->getFirstItem();
		return $pickup;
	}
}
?>