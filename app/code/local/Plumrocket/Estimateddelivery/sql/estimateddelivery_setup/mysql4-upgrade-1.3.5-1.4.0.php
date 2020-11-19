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

$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('eav/entity_setup', 'core_setup');

// ======================== CATEGORY

$entityTypeId = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping');

// Add Enable Delivery
$setup->addAttribute('catalog_category', 'estimated_delivery_days',  array(
    'type'          => 'varchar',
    'label'         => 'Business Days For Delivery',
    'input'         => 'text',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => true,
    'required'      => false,
    'user_defined'  => false,
    'note'          => "Number of business days (excluding weekends and holidays) from today's date required for the product to be delivered.",
    'default'       => '',
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_days', '25');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_days','25');

// Add Shipping Text
$setup->addAttribute('catalog_category', 'estimated_shipping_days', array(
    'type'          => 'varchar',
    'label'         => 'Business Days To Shipping',
    'input'         => 'text',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => 1,
    'required'      => 0,
    'user_defined'  => 0,
    'note'          => "Number of business days (excluding weekends and holidays) from today's date before product is shipped. Will be used if no specific shipping date nor estimated shipping text provided.",
    'default'       => '',
));

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_days', '65');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_days', '65');

// ======================== PRODUCT

$entityTypeId = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping');

// Add Enable Delivery
$setup->addAttribute('catalog_product', 'estimated_delivery_days',  array(
    'group'         => 'Estimated Delivery/Shipping',
    'type'          => 'varchar',
    'label'         => 'Business Days For Delivery',
    'input'         => 'text',
    'visible'       => true,
    'required'      => false,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'note'          => "Number of business days (excluding weekends and holidays) from today's date required for the product to be delivered.",
    'default'       => '',
    'sort_order'    => 255
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_days', '255');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_days','255');

// Add Shipping Text
$setup->addAttribute('catalog_product', 'estimated_shipping_days', array(
    'group'         => 'Estimated Delivery/Shipping',
    'type'          => 'varchar',
    'label'         => 'Business Days To Shipping',
    'input'         => 'text',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'note'          => "Number of business days (excluding weekends and holidays) from today's date before product is shipped. Will be used if no specific shipping date nor estimated shipping text provided.",
    'sort_order'    => 325
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_days', '325');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_days', '325');



$installer->endSetup();