<?php 

class MST_Storepickup_Block_Adminhtml_Storepickup_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('storepickup_form', array('legend' => Mage::helper('storepickup')->__('Item information')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('storepickup')->__('Store Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
		$fieldset->addField('storepickup_status', 'select', array(
            'label' => Mage::helper('storepickup')->__('Status'),
            'name' => 'storepickup_status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('storepickup')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('storepickup')->__('Disabled'),
                ),
            ),
        ));
		if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores_id', 'multiselect', array(
                    'name'      => 'stores_id',
                    'label'     => Mage::helper('storepickup')->__('Store View'),
                    'title'     => Mage::helper('storepickup')->__('Store View'),
                    'required'  => true,
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
                $fieldset->addField('stores_id', 'hidden', array(
                    'name'      => 'stores_id[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                ));
        }
		$fieldset->addField('address', 'text', array(
            'label' => Mage::helper('storepickup')->__('Address'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'address',
        ));
		
		$fieldset->addField('city', 'text', array(
            'label' => Mage::helper('storepickup')->__('City'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'city',
        ));
		
		$fieldset->addField('zipcode', 'text', array(
            'label' => Mage::helper('storepickup')->__('Zip Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'zipcode',
        ));
		
		$fieldset->addField('country', 'select', array(
            'label' => Mage::helper('storepickup')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'country',
            'values' => Mage::helper('storepickup')->getOptionCountry(),
        ));
		
		$fieldset->addField('stateEl', 'note', array(
            'label' => Mage::helper('storepickup')->__('State/Province'),
            'name' => 'stateEl',
            'text' => $this->getLayout()->createBlock('storepickup/adminhtml_region')->setTemplate('storepickup/region.phtml')->toHtml(),
        ));
		$fieldset->addField('phone_number', 'text', array(
            'label' => Mage::helper('storepickup')->__('Phone Number'),
            'class' => '',
            'required' => false,
            'name' => 'phone_number',
        ));
		$fieldset->addField('fax_number', 'text', array(
            'label' => Mage::helper('storepickup')->__('Fax Number'),
            'class' => '',
            'required' => false,
            'name' => 'fax_number',
        ));
		$fieldset->addField('email', 'text', array(
            'label' => Mage::helper('storepickup')->__('Email Address'),
            'class' => '',
            'required' => false,
            'name' => 'email',
        ));
		
		
        
        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('storepickup')->__('Description'),
            'title' => Mage::helper('storepickup')->__('Description'),
            'style' => 'width:700px; height:150px;',
            'wysiwyg' => false,
            'required' => true,
        ));
        $fieldset->addField('position', 'text', array(
            'label' => Mage::helper('storepickup')->__('Position'),
            'class' => '',
            'required' => false,
            'name' => 'position',
        ));
				
		$fieldset->addField('mapicon', 'image', array(
            'label' => Mage::helper('storepickup')->__('Custom Map Location Icon'),
            'required' => false,
			'class' => 'require-entry',
            'name' => 'mapicon',
        ));
		
		
		
	  
		if(isset($data['image_icon']) && $data['image_icon']){
			$data['image_icon'] = 'storepickup/images/icon/' . $data['storepickup_id'] . '/' . $data['image_icon'];
		}  

		$fieldset->addField('image_id', 'text', array(
            'label' => Mage::helper('storepickup')->__('Store Image(s)'),
            'name' => 'images_id',
            'value' => Mage::helper('storepickup')->getDataImage($this->getRequest()->getParam('id')),
        ))->setRenderer($this->getLayout()->createBlock('storepickup/adminhtml_grid_renderer_storeimage'));
		
		$fieldset->addField('latitude', 'text', array(
			'label'     => Mage::helper('storepickup')->__('Latitude'),
			'class' => 'required-entry',
			'required' => true,                
			'name'      => 'latitude',
		 ));
		$fieldset->addField('longitude', 'text', array(
			'label'     => Mage::helper('storepickup')->__('Longitude'),
			'class' => 'required-entry',
			'required' => true,                
			'name'      => 'longitude',
		 ));
		 
		 $fieldset->addField('zoom_level', 'text', array(
			'label'     => Mage::helper('storepickup')->__('Zoom Level'),
			'class' => 'required-entry',
			'required' => true,                
			'name'      => 'zoom_level',
		 ));
		
		$fieldset->addField('gmap', 'text', array(	
                'label'     => Mage::helper('storepickup')->__('Store Map'), 
                'name'		=> 'gmap',                       
            ))->setRenderer($this->getLayout()->createBlock('storepickup/adminhtml_gmap'));     
            
		
		
        if (Mage::getSingleton('adminhtml/session')->getStorepickupData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStorepickupData());
            Mage::getSingleton('adminhtml/session')->setStorepickupData(null);
        } elseif (Mage::registry('storepickup_data')) {
            $form->setValues(Mage::registry('storepickup_data')->getData());
        }
        return parent::_prepareForm();
    }
}