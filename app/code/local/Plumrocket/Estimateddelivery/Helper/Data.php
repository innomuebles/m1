<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package	Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Helper_Data extends Plumrocket_Estimateddelivery_Helper_Main
{
	public function moduleEnabled($store = null)
	{
		return (bool)Mage::getStoreConfig('estimateddelivery/general/enable', $store);
	}

	public function disableExtension()
	{
		$resource = Mage::getSingleton('core/resource');
		$connection = $resource->getConnection('core_write');
		$connection->delete($resource->getTableName('core/config_data'), array($connection->quoteInto('path IN (?)', array('estimateddelivery/general/enable', 'estimateddelivery/delivery/default_days', 'estimateddelivery/delivery/default_text', 'estimateddelivery/shipping/default_text'))));
		$config = Mage::getConfig();
		$config->reinit();
		Mage::app()->reinitStores();
	}

	public function makeDeliveryGroup($group)
    {
        $deliveryGroup = new Varien_Object($group->getData());
        $deliveryGroup->setAttributeGroupName($this->__('Estimated Delivery Date'));
        return $deliveryGroup;
    }

    public function makeShippingGroup($group)
    {
        $shippingGroup = new Varien_Object($group->getData());
        $shippingGroup->setAttributeGroupName($this->__('Estimated Shipping Date'));
        return $shippingGroup;
    }

    public function getGroup($setId)
    {
        $groupName = $this->getGroupName();

        return Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->setAttributeSetFilter($setId)
            ->addFilter('attribute_group_name', $groupName)
            ->setSortOrder()
            ->load()
            ->getFirstItem();
    }

    public function getGroupName()
    {  
        return 'Estimated Delivery/Shipping';
    }

    public function showPosition($position)
    {
        $positions = explode(',', Mage::getStoreConfig('estimateddelivery/general/position'));
        if(in_array($position, $positions)) {
            return true;
        }
    }

    public function moduleCheckoutspageEnabled()
    {
        $hasModule = Mage::helper('core')->isModuleEnabled('Plumrocket_Checkoutspage');
        if($hasModule) {
            return true;//Mage::helper('checkoutspage')->moduleEnabled();
        }

        return false;
    }

    public function getDateTimeFormat()
    {
        // return 'M/d/yyyy H:mm';
        return 'MM-dd-yyyy';
    }

}
	 