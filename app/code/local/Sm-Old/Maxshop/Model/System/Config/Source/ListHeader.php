<?php
/*------------------------------------------------------------------------
 # SM Maxshop - Version 1.1
 # Copyright (c) 2013 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Maxshop_Model_System_Config_Source_ListHeader
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'df', 'label'=>Mage::helper('maxshop')->__('Default')),
		array('value'=>'hd2', 'label'=>Mage::helper('maxshop')->__('Header 2')),
		array('value'=>'hd3', 'label'=>Mage::helper('maxshop')->__('Header 3')),
		array('value'=>'hd4', 'label'=>Mage::helper('maxshop')->__('Header 4')),
		array('value'=>'hd5', 'label'=>Mage::helper('maxshop')->__('Header 5'))
		);
	}
}
