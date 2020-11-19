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

class Plumrocket_Estimateddelivery_Model_Observer
{
    public function loadCategoryTabs($observer)
    {
        $helper = Mage::helper('estimateddelivery');
        if(!$helper->moduleEnabled()) {
            return;
        }

        $tabs = $observer->getEvent()->getTabs();
        $attributeSetId = $tabs->getCategory()->getDefaultAttributeSetId();
        $group = $helper->getGroup($attributeSetId);

        if ($group && $group->getId()) {
            $deliveryAttributes = array();
            $shippingAttributes = array();

            $categoryAttributes = $tabs->getCategory()->getAttributes();
            foreach ($categoryAttributes as $attribute) {
                if ($attribute->isInGroup($attributeSetId, $group->getId())) {
                    if (strpos($attribute->getAttributeCode(), 'estimated_delivery') !== false) {
                        $deliveryAttributes[] = $attribute;
                    } else {
                        $shippingAttributes[] = $attribute;
                    }
                }
            }

            $html = $tabs->getLayout()->createBlock($tabs->getAttributeTabBlock(), '')
                ->setGroup($helper->makeDeliveryGroup($group))
                ->setAttributes($deliveryAttributes)
                ->setAddHiddenFields(false)
                ->toHtml();

            $html .= $tabs->getLayout()->createBlock($tabs->getAttributeTabBlock(), '')
                ->setGroup($helper->makeShippingGroup($group))
                ->setAttributes($shippingAttributes)
                ->setAddHiddenFields(false)
                ->toHtml();

            $html .= '<script type="text/javascript">refreshEstimatedData();</script>';

            $tabs->setTabData('group_' . $group->getId(), 'content', $html);
        }
        return $observer;
    }

    public function prepareProductForm($observer)
    {
        /*
        $form = $observer->getEvent()->getForm();
        
        $ranges = array(
            'estimated_delivery_date_from'  => 'estimated_delivery_date_to',
            'estimated_shipping_date_from'  => 'estimated_shipping_date_to',
            'estimated_delivery_days_from'  => 'estimated_delivery_days_to',
            'estimated_shipping_days_from'  => 'estimated_shipping_days_to',
        );

        foreach ($ranges as $master => $slave) {
            $block = $form->getElement($master);
            if ($block) {
                $block->setRenderer(
                    Mage::app()->getLayout()->createBlock('estimateddelivery/adminhtml_fields_master')
                        ->setSlave($form->getElement($slave))
                );
            }
        }
        */

        return $observer;
    }




    public function coreBlockAbstractToHtmlBefore($observer)
    {
        $block = $observer->getBlock();
        $request = Mage::app()->getRequest();
        $helper = Mage::helper('estimateddelivery');

        if(!$helper->moduleEnabled()) {
            return;
        }

        switch (true) {
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::SHOPPING_CART)
                && $block instanceof Mage_Checkout_Block_Cart_Item_Renderer
                && $request->getModuleName()        == 'checkout'
                && $request->getControllerName()    == 'cart':
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::SHOPPING_CART)
                && $block instanceof Mage_Checkout_Block_Cart_Item_Renderer
                && (in_array($request->getModuleName(), array('cms', 'catalog', 'ajaxcart', 'customer'))):
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::CHECKOUT)
                && $block instanceof Mage_Checkout_Block_Cart_Item_Renderer
                && false !== strpos($request->getModuleName(), 'checkout')
                && $request->getControllerName()    == 'onepage'
                && $request->getActionName()        != 'success':
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::CHECKOUT)
                && $block instanceof Mage_Checkout_Block_Cart_Item_Renderer
                && $request->getModuleName() == 'onestepcheckout':

                if($item = $block->getItem()) {
                    if (!$item->getPrEdOption()) {
                        $item->setPrEdOption(1);
                        if ($options = $this->_getEstimatedDeliveryOptions($item)) {
                            $item->addOption(array(
                                'code' => 'additional_options',
                                'value' => serialize($options))
                            );
                        }
                    }
                }
                break;

            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::PM_ORDER_SUCCESS)
                && $block instanceof Mage_Checkout_Block_Cart_Item_Renderer
                && $request->getModuleName()        == 'checkout'
                && $request->getControllerName()    == 'onepage'
                && $request->getActionName()        == 'success':
            /*case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::PM_ORDER_SUCCESS)
                && $block instanceof Mage_Sales_Block_Order_Email_Items_Order_Default
                && $request->getModuleName()        == 'checkoutspage'
                && $request->getControllerName()    == 'preview'
                && $request->getActionName()        == 'email':*/
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::CUSTOMER_ORDER)
                && $block instanceof Mage_Sales_Block_Order_Item_Renderer_Default
                && $request->getModuleName()        == 'sales'
                && $request->getControllerName()    == 'order':
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::ORDER_CONFIRMATION)
                && $block instanceof Mage_Sales_Block_Order_Email_Items_Order_Default
                && $block->getRenderedBlock() instanceof Mage_Sales_Block_Order_Email_Items:
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::INVOICE)
                && $block instanceof Mage_Sales_Block_Order_Email_Items_Default
                && $block->getRenderedBlock() instanceof Mage_Sales_Block_Order_Email_Invoice_Items
                && $item = $block->getItem()->getOrderItem():
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::SHIPMENT)
                && $block instanceof Mage_Sales_Block_Order_Email_Items_Default
                && $block->getRenderedBlock() instanceof Mage_Sales_Block_Order_Email_Shipment_Items
                && $item = $block->getItem()->getOrderItem():
            case $helper->showPosition(Plumrocket_Estimateddelivery_Model_Values_Position::ADMINPANEL_ORDER)
                && $block instanceof Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default //Mage_Adminhtml_Block_Sales_Items_Abstract
                && $request->getControllerName()    == 'sales_order'
                && $request->getActionName()        == 'view':

                if(empty($item)) {
                    $item = $block->getItem();
                }

                if($item) {
                    if($options = $this->_getEstimatedDeliveryOptions($item)) {

                        $itemOptions = $item->getProductOptions();

                        if(is_null($itemOptions)) {
                            $itemOptions = array();
                        }

                        if (!is_array($itemOptions)) {
                            if($itemOptions = @unserialize($itemOptions)) {
                                $doSerialize = true;
                            }
                        }

                        // If use key "additional_options", delivery data will display before configurable attributes of product.
                        if(empty($itemOptions['attributes_info'])) {
                            $itemOptions['attributes_info'] = array();
                        }

                        // If option is already added, skip it.
                        foreach ($itemOptions['attributes_info'] as $element) {
                            foreach ($options as $n => $option) {
                                if ($element == $option) {
                                    unset($options[$n]);
                                }
                            }
                        }

                        $itemOptions['attributes_info'] = array_merge($itemOptions['attributes_info'], $options);
                        if(!empty($doSerialize)) {
                            $itemOptions = serialize($itemOptions);
                        }
                        $item->setProductOptions($itemOptions);
                    }
                }
                break;
        }
    }

    protected function _getEstimatedDeliveryOptions($item)
    {
        $options = array();

        if(!$product = $item->getProduct()) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
        }

        $estimateddelivery = Mage::app()->getLayout()
            ->createBlock('estimateddelivery/product')
            ->setProduct($product);

        if ($shipping = $estimateddelivery->getShipping()) {
            $options[] = $shipping;
        }
        
        if ($delivery = $estimateddelivery->getDelivery()) {
            $options[] = $delivery;
        }

        return $options;
    }



}
