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

// Add catalog's tab
$setup->addAttributeGroup($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping', 40);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping');

// Add Enable Delivery
$setup->addAttribute('catalog_category', 'estimated_delivery_enable',  array(
    'type'     		=> 'int',
    'label'    		=> 'Estimated Delivery Status',
    'input'    		=> 'select',
    'global'   		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => true,
    'required'      => true,
    'user_defined'  => false,
	'source'        => 'estimateddelivery/attribute_source_enable',
	'default'       => 0,
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_enable', '10');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_enable','10');

// Move delivery fields to the new tab
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '20');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '20');

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '30');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '30');

// Add Enable Shipping
$setup->addAttribute('catalog_category', 'estimated_shipping_enable',  array(
    'type'     		=> 'int',
    'label'    		=> 'Estimated Shipping Status',
    'input'    		=> 'select',
    'global'   		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'       => true,
    'required'      => true,
    'user_defined'  => false,
	'source'        => 'estimateddelivery/attribute_source_enable',
	'default'       => 0,
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_enable', '40');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_enable','40');

// Add Shipping From
$setup->addAttribute('catalog_category', 'estimated_shipping_date_from', array(
	'type'          => 'datetime',
	'backend'       => 'eav/entity_attribute_backend_datetime',
	'label'         => 'Estimated Shipping Date From',
	'input'         => 'date',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'       => 1,
	'required'      => 0,
	'user_defined'  => 0,
	'default'       => '',
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_from', '50');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_from', '50');

// Add Shipping To
$setup->addAttribute('catalog_category', 'estimated_shipping_date_to', array(
	'type'          => 'datetime',
	'backend'       => 'eav/entity_attribute_backend_datetime',
	'label'         => 'Estimated Shipping Date To',
	'input'         => 'date',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'       => 1,
	'required'      => 0,
	'user_defined'  => 0,
	'default'       => '',
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_to', '60');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_date_to', '60');

// Add Shipping Text
$setup->addAttribute('catalog_category', 'estimated_shipping_text', array(
	'type'          => 'varchar',
	'label'         => 'Estimated Shipping Text',
	'input'         => 'text',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'       => 1,
	'required'      => 0,
	'user_defined'  => 0,
	'default'       => '',
	// 'note'			=> "Text field has higher priority over the estimated shipping date fields. Make sure it is empty if you don't want the text to be displayed."
));

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_text', '70');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_shipping_text', '70');

// ======================== PRODUCT

$entityTypeId = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);

$setup->addAttributeGroup($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping', 500);
$attributeGroupId = $setup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Estimated Delivery/Shipping');

// Add Enable Delivery
$setup->addAttribute('catalog_product', 'estimated_delivery_enable',  array(
	'input'    		=> 'select',
    'type'     		=> 'int',
    'label'    		=> 'Estimated Delivery Status',
    'visible'       => true,
    'required'      => true,
    'visible_on_front' => 1,
    'global'   		=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'default'       => 0,
	'source'        => 'estimateddelivery/attribute_source_enable',
));
$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_enable', '240');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_enable','240');

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_date', '250');

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '260');
$setup->addAttributeToSet($entityTypeId, $attributeSetId, $attributeGroupId, 'estimated_delivery_text', '260');

// Add Enable Shipping
$setup->addAttribute('catalog_product', 'estimated_shipping_enable',  array(
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