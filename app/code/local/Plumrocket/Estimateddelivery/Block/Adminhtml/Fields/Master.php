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

class Plumrocket_Estimateddelivery_Block_Adminhtml_Fields_Master extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    protected $_slave = null;

	public function getElementHtml()
	{
		$html = $this->getElement()->getElementHtml();
		if ($slave = $this->getSlave()) {
			$html .= ' — ' . $slave->getElementHtml();

			$slave->setRenderer(
                Mage::app()->getLayout()->createBlock('estimateddelivery/adminhtml_fields_slave')
            );
		}
		return $html;
	}

	public function setSlave($slave)
    {
    	$this->_slave = $slave;
    	return $this;
    }

    public function getSlave()
    {
    	return $this->_slave;
    }
}