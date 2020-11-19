<?php
class MST_Storepickup_IndexController extends Mage_Core_Controller_Front_Action/** * @Author      : Magebay Team * @package     Store Pick Up * @copyright   Copyright (c) 2014 MAGEBAY (http://www.magebay.com) * @terms  http://www.magebay.com/terms * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) **/
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    public function viewAction()
    {
        $storepickup_id = $this->getRequest()->getParam('id');
        if ($storepickup_id != null && $storepickup_id != '') {
            $storepickup = Mage::getModel('storepickup/storepickup')->load($storepickup_id);
        } else {
            $storepickup = null;
        }
        Mage::register('storepickup', $storepickup);
        $this->loadLayout();
        $this->renderLayout();
    }
    function savecontactAction()
	{
		if($data = $this->getRequest()->getPost())
		{
			foreach($data as $key => $value)
			{
				if(trim($value) == '')
				{
					 Mage::getSingleton('core/session')->addError($this->__('Please enter value All field'));
					$this->_redirect('*/*/view/id/'.$data['pickup_id']);
					return;
				}
			}
			if($data['contact_email'] != '')
			{
				if(!filter_var($data['contact_email'],FILTER_VALIDATE_EMAIL))
				{
					Mage::getSingleton('core/session')->addError($this->__('Email Not allow'));
					$this->_redirect('*/*/view/id/'.$data['pickup_id']);
					return;
				}
			}
			$dataContact = array();
			$dataContact['pickup_contact_name'] = $data['contact_name'];
			$dataContact['pickup_contact_email'] = $data['contact_email'];
			$dataContact['pickup_contact_message'] = $data['contact_content'];
			$dataContact['pickup_contact_at'] = date('Y-m-d H:i:s');
			$dataContact['pickup_id'] = $data['pickup_id'];
			$contact = Mage::getModel('storepickup/messages');
			$contact->setData($dataContact);
			$contact->save();
			Mage::getSingleton('core/session')->addSuccess($this->__('Send contact Success'));
			$this->_redirect('*/*/index');
			return;
		}
		Mage::getSingleton('core/session')->addError($this->__('Form Not submit'));
		$this->_redirect('*/*/index');
		return;
	}	function testAction()    {		$order = Mage::getModel('sales/order')->load(14);
		$a = $order->getStorePickUpAddress();
		Zend_debug::dump($a);    }}