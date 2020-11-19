<?php

class MST_Storepickup_Block_Adminhtml_Storepickup extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_storepickup';
        $this->_blockGroup = 'storepickup';
        $this->_headerText = Mage::helper('storepickup')->__('Manage Store');
        $this->_addButtonLabel = Mage::helper('storepickup')->__('Add Item');
        parent::__construct();
    }
	
	public function getLocation()
    {
		$id = $this->getRequest()->getParam('id');
		if(!$id) return null;
		$store = Mage::getModel('storepickup/storepickup')->load($id);
		
       if($store){
            $location['latitude'] = $store->getLatitude();
            $location['longtitude'] = $store->getLongtitude();
            $location['zoom_level'] = $store->getZoom_level(); 
            
            return $location;
       }else{
           return null;
       }
    }	

}