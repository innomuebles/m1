<?php
/*------------------------------------------------------------------------
 # SM Image Slider - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Imageslider_Model_System_Config_Source_Effect
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'slide',			'label' => Mage::helper('imageslider')->__('Slide')),
			array('value' => 'fade',			'label' => Mage::helper('imageslider')->__('Fade')),
			array('value' => 'backSlide', 		'label' => Mage::helper('imageslider')->__('BackSlide')),
			array('value' => 'goDown',			'label' => Mage::helper('imageslider')->__('GoDown')),
			array('value' => 'fadeUp', 		'label' => Mage::helper('imageslider')->__('FadeUp'))
			
		);
	}
}
