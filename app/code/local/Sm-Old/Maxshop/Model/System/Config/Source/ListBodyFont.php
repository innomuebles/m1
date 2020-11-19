<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Maxshop_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'Arial', 'label'=>Mage::helper('maxshop')->__('Arial')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('maxshop')->__('Open Sans')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('maxshop')->__('Arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('maxshop')->__('Courier New')),
			array('value'=>'Georgia', 'label'=>Mage::helper('maxshop')->__('Georgia')),
			array('value'=>'Impact', 'label'=>Mage::helper('maxshop')->__('Impact')),
			array('value'=>'Lucida Console', 'label'=>Mage::helper('maxshop')->__('Lucida-console')),
			array('value'=>'Lucida Grande', 'label'=>Mage::helper('maxshop')->__('Lucida-grande')),
			array('value'=>'Palatino', 'label'=>Mage::helper('maxshop')->__('Palatino')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('maxshop')->__('Tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('maxshop')->__('Times New Roman')),	
			array('value'=>'Trebuchet', 'label'=>Mage::helper('maxshop')->__('Trebuchet')),	
			array('value'=>'Verdana', 'label'=>Mage::helper('maxshop')->__('Verdana'))		
		);
	}
}
