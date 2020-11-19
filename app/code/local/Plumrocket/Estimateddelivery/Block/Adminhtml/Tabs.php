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

class Plumrocket_Estimateddelivery_Block_Adminhtml_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	private $parent;

	protected function _prepareLayout()
	{
		$this->parent = parent::_prepareLayout();
		$product = $this->getProduct();

		if (!($setId = $product->getAttributeSetId())) {
			$setId = $this->getRequest()->getParam('set', null);
		}

		if ($setId) {
			$group = Mage::helper('estimateddelivery')->getGroup($setId);

			if ($group && $group->getId()) {
				$attributes = $product->getAttributes($group->getId(), true);
				$deliveryAttributes = array();
				$shippingAttributes = array();

				foreach ($attributes as $attribute) {
	                if (strpos($attribute->getAttributeCode(), 'estimated_delivery') !== false) {
	                	$deliveryAttributes[] = $attribute;
	                } else {
	                	$shippingAttributes[] = $attribute;
	                }
	            }

	            $html = $this->getLayout()->createBlock($this->getAttributeTabBlock())
	            	->setGroup(Mage::helper('estimateddelivery')->makeDeliveryGroup($group))
					->setGroupAttributes($deliveryAttributes)
					->toHtml();

				$html .= $this->getLayout()->createBlock($this->getAttributeTabBlock())
					->setGroup(Mage::helper('estimateddelivery')->makeShippingGroup($group))
					->setGroupAttributes($shippingAttributes)
					->toHtml();

				$this->setTabData('group_'.$group->getId(), 'content', $this->_translateHtml($html));
			}
		}
		return $this->parent;
	}
}