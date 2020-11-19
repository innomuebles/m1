<?php

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.

@package  Plumrocket_Estimated_Delivery_Date-v1.1.x
@copyright  Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license  http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement

*/

class Plumrocket_Estimateddelivery_Block_System_Config_Editor extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setWysiwyg(true);
        $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $config->setData('add_widgets', 0);
        $config->setData('add_images', 0);

        $plugins = $config->getPlugins();
        foreach ($plugins as $key => $item) {
            if ($item['name'] == 'magentovariable') {
                unset($plugins[$key]);
            }
        }
        $config->setPlugins($plugins);

        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }
}
