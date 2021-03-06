<?php
class MST_Storepickup_Block_System_Config_Form_Field_Notification extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
      //  $element->setValue(Mage::app()->loadCache('admin_notifications_lastcheck'));
      //  $format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
	  
		$main_domain = Mage::helper('storepickup')->get_domain( $_SERVER['SERVER_NAME'] );
		if ( $main_domain != 'dev' ) {
		$rakes = Mage::getModel('storepickup/license')->getCollection();
		$rakes->addFieldToFilter('path', 'storepickup/license/key' );
		$valid = false;
		
			if ( count($rakes) > 0 ) {
				foreach ( $rakes as $rake )  {
					if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('storepickup/license/key')) ) ) {
						$valid = true;	
					}
				}
			}
			
			$html = base64_decode('PHAgc3R5bGU9ImNvbG9yOiByZWQ7Ij48Yj5OT1QgVkFMSUQ8L2I+PC9wPg==');	
			
			if ( $valid == true ) {
			//if ( count($rakes) > 0 ) {  
				foreach ( $rakes as $rake )  {
					if ( $rake->getExtensionCode() == md5($main_domain.trim(Mage::getStoreConfig('storepickup/license/key')) ) ) {
						$html = base64_decode('PGhyIHdpZHRoPSIyODAiPjxiPltEb21haW5Db3VudF0gRG9tYWluIExpY2Vuc2U8L2I+PGJyPjxiPkFjdGl2ZSBEYXRlOiA8L2I+W0NyZWF0ZWRUaW1lXTxicj48Yj5Eb21haW4ocyk6PC9iPiBbRG9tYWluTGlzdF08L2JyPg==');	
						$html = str_replace(array('[DomainCount]','[CreatedTime]','[DomainList]'),array($rake->getDomainCount(),$rake->getCreatedTime(),$rake->getDomainList()),$html);
					}
				}
			}
		} else { 
		$html = base64_decode('');	
		}
		
		
        return $html;
    }
}