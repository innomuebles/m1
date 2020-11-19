<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Maxshop_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'blue', 'label'=>Mage::helper('maxshop')->__('Blue')),
		array('value'=>'red', 'label'=>Mage::helper('maxshop')->__('Red')),
		array('value'=>'green', 'label'=>Mage::helper('maxshop')->__('Green')),
		array('value'=>'cyan', 'label'=>Mage::helper('maxshop')->__('Cyan')),
		array('value'=>'orange', 'label'=>Mage::helper('maxshop')->__('Orange'))
		);
	}
}
