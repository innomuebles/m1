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

class Plumrocket_Estimateddelivery_Block_System_Config_Days extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$privatesalesEnabled = Mage::getConfig()->getNode('modules/Plumrocket_Privatesales') 
								&& ! (int)Mage::getStoreConfigFlag('advanced/modules_disable_output/Plumrocket_Privatesales');

		return ($privatesalesEnabled)? parent::render($element): '';
	}
}