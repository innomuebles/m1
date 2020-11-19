<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package    Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright  Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license    http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Estimateddelivery_Block_System_Config_Renderer_Button extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

	public function render(Varien_Object $row)
    {
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->addData(array(
                'id'      => 'estimateddelivery_remove_button',
                'label'   => $this->__('Remove'),
                'type'    => 'button',
                'class'   => 'delete dateperiod-remove',
            ))
            ->toHtml();
    }

}