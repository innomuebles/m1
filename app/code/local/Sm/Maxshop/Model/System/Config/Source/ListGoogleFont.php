<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Maxshop_Model_System_Config_Source_ListGoogleFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'', 'label'=>Mage::helper('maxshop')->__('No select')),
			array('value'=>'Dosis Regular', 'label'=>Mage::helper('maxshop')->__('Dosis Regular')),
			array('value'=>'Anton', 'label'=>Mage::helper('maxshop')->__('Anton')),
			array('value'=>'Questrial', 'label'=>Mage::helper('maxshop')->__('Questrial')),
			array('value'=>'Kameron', 'label'=>Mage::helper('maxshop')->__('Kameron')),
			array('value'=>'Oswald', 'label'=>Mage::helper('maxshop')->__('Oswald')),
			array('value'=>'Open Sans', 'label'=>Mage::helper('maxshop')->__('Open Sans')),
			array('value'=>'BenchNine', 'label'=>Mage::helper('maxshop')->__('BenchNine')),
			array('value'=>'Droid Sans', 'label'=>Mage::helper('maxshop')->__('Droid Sans')),
			array('value'=>'Droid Serif', 'label'=>Mage::helper('maxshop')->__('Droid Serif')),
			array('value'=>'PT Sans', 'label'=>Mage::helper('maxshop')->__('PT Sans')),
			array('value'=>'Vollkorn', 'label'=>Mage::helper('maxshop')->__('Vollkorn')),
			array('value'=>'Ubuntu', 'label'=>Mage::helper('maxshop')->__('Ubuntu')),
			array('value'=>'Neucha', 'label'=>Mage::helper('maxshop')->__('Neucha')),
			array('value'=>'Cuprum', 'label'=>Mage::helper('maxshop')->__('Cuprum'))	
		);
	}
}
