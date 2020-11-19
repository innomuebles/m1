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

$entityTypeId = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping');

// Add Enable Delivery
$setup->addAttribute('catalog_product', 'estimated_delivery_enable',  array(
	'group'         => 'Estimated Delivery/Shipping',
	'input'    		=> 'select',
    'type'     		=> 'int',
    'label'    		=> 'Estimated Delivery Status',
    'visible'       => true,
    'required'      => true,
    'visible_on_front' => 1,
    'global'   		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'default'       => 0,
	'source'        => 'estimateddelivery/attribute_source_enable',
	'sort_order'	=> 240
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_enable', '240');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_enable','240');

// Move fields to new group
/*
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '260');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '260');
 * 
 */

$setup->addAttribute('catalog_product', 'estimated_delivery_date', array(
    'group'         => 'Estimated Delivery/Shipping',
    'input'         => 'date',
    'type'          => 'datetime',
    'label'         => 'Estimated Delivery Date',
    'backend'       => 'eav/entity_attribute_backend_datetime',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'sort_order'	=> 250
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');

$setup->addAttribute('catalog_product', 'estimated_delivery_text', array(
    'group'         => 'Estimated Delivery/Shipping',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Estimated Delivery Text',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	// 'note'			=> "Text field has higher priority over the estimated delivery date field. Make sure it is empty if you don't want text to be displayed.",
	'sort_order'	=> 260
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '260');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '260');

// Add Enable Shipping
$setup->addAttribute('catalog_product', 'estimated_shipping_enable',  array(
	'group'         => 'Estimated Delivery/Shipping',
	'input'    		=> 'select',
    'type'     		=> 'int',
    'label'    		=> 'Estimated Shipping Status',
    'visible'       => true,
    'required'      => true,
    'visible_on_front' => 1,
    'global'   		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'default'       => 0,
	'source'        => 'estimateddelivery/attribute_source_enable',
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_enable', '300');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_enable','300');

// Add Shipping From
$setup->addAttribute('catalog_product', 'estimated_shipping_date_from', array(
	'group'         => 'Estimated Delivery/Shipping',
    'input'         => 'date',
    'type'          => 'datetime',
    'label'         => 'Estimated Shipping Date From',
    'backend'       => 'eav/entity_attribute_backend_datetime',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_from', '310');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_from', '310');

// Add Shipping To
$setup->addAttribute('catalog_product', 'estimated_shipping_date_to', array(
	'group'         => 'Estimated Delivery/Shipping',
    'input'         => 'date',
    'type'          => 'datetime',
    'label'         => 'Estimated Shipping Date To',
    'backend'       => 'eav/entity_attribute_backend_datetime',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_to', '320');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_to', '320');

// Add Shipping Text
$setup->addAttribute('catalog_product', 'estimated_shipping_text', array(
	'group'         => 'Estimated Delivery/Shipping',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Estimated Shipping Text',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	// 'note'			=> "Text field has higher priority over the estimated shipping date fields. Make sure it is empty if you don't want the text to be displayed."
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_text', '330');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_text', '330');



$installer->endSetup();