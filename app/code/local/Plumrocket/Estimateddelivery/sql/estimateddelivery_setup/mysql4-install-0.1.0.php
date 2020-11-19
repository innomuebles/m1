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

$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('eav/entity_setup', 'core_setup');

$entityTypeId = $setup->getEntityTypeId('catalog_category');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General Information');

// ======================== CATEGORY
$setup->addAttribute('catalog_category', 'estimated_delivery_date', array(
	'type'              => 'datetime',
	'backend'           => 'eav/entity_attribute_backend_datetime',
	'label'             => 'Estimated Delivery Date',
	'input'             => 'date',
	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'           => 1,
	'required'          => 0,
	'user_defined'      => 0,
	'default'           => '',
	'position'          => 250,
));

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');

// ========================
$setup->addAttribute('catalog_category', 'estimated_delivery_text', array(
	'type'              => 'varchar',
	'label'             => 'Estimated Delivery Text',
	'input'             => 'text',
	'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'           => 1,
	'required'          => 0,
	'user_defined'      => 0,
	'default'           => '',
	'position'          => 255,
	// 'note'				=> "Text field has higher priority over the estimated delivery date field. Make sure it is empty if you don't want text to be displayed."
));

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '255');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '255');

// ======================== PRODUCT

$entityTypeId = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
//$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'General');

$setup->addAttributeGroup($entityTypeId, $attributeSetId, 'Estimated Delivery', 500);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Estimated Delivery');

$setup->addAttribute('catalog_product', 'estimated_delivery_date', array(
    'group'         => 'Estimated Delivery',
    'input'         => 'date',
    'type'          => 'datetime',
    'label'         => 'Estimated Delivery Date',
    'backend'       => 'eav/entity_attribute_backend_datetime',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');

$setup->addAttribute('catalog_product', 'estimated_delivery_text', array(
    'group'         => 'Estimated Delivery',
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Estimated Delivery Text',
    'backend'       => '',
    'visible'       => 1,
    'required'      => 0,
    'visible_on_front' => 1,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	// 'note'			=> "Text field has higher priority over the estimated delivery date field. Make sure it is empty if you don't want text to be displayed."
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '255');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '255');


$installer->endSetup();