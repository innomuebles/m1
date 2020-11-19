<?php
class MST_Storepickup_Block_Adminhtml_Storepickup_Edit_Tab_Schedule extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('storepickup_timeschedule', array('legend' => Mage::helper('storepickup')->__('Time Schedule')));
         $fieldset->addField('pickup_monday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Monday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_monday_open',
            'after_element_html'=>'<small>'.Mage::helper('storepickup')->__('Format example 09:00').'</small>',
        ));
        $fieldset->addField('pickup_monday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Monday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_monday_close',
            'after_element_html'=>'<small>'.Mage::helper('storepickup')->__('Format example 23:00').'</small>',
        ));
          $fieldset->addField('pickup_tuesday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Tuesday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_tuesday_open',
        ));
        $fieldset->addField('pickup_tuesday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Tuesday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_tuesday_close',
        )); $fieldset->addField('pickup_wednesday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Wednesday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_wednesday_open',
        ));
        $fieldset->addField('pickup_wednesday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Wednesday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_wednesday_close',
        )); $fieldset->addField('pickup_thursday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Thursday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_thursday_open',
        ));
        $fieldset->addField('pickup_thursday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Thursday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_thursday_close',
        )); $fieldset->addField('pickup_friday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Friday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_friday_open',
        ));
        $fieldset->addField('pickup_friday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Friday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_friday_close',
        )); $fieldset->addField('pickup_saturday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Saturday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_saturday_open',
        ));
        $fieldset->addField('pickup_saturday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Saturday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_saturday_close',
        )); $fieldset->addField('pickup_sunday_open', 'text', array(
            'label' => Mage::helper('storepickup')->__('Sunday Open Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_sunday_open',
        ));
        $fieldset->addField('pickup_sunday_close', 'text', array(
            'label' => Mage::helper('storepickup')->__('Sunday Close Time'),
            'class' => '',
            'required' => false,
            'name' => 'pickup_sunday_close',
        ));
        if (Mage::getSingleton('adminhtml/session')->getStorepickupproData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getStorepickuppro());
            Mage::getSingleton('adminhtml/session')->setFileUploaderData(null);
        } elseif (Mage::registry('storepickup_data')) {
            $form->setValues(Mage::registry('storepickup_data')->getData());
        }
        return parent::_prepareForm();
    }

}
?>