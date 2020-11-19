<?php
class MST_Storepickup_Model_Sales_Order extends Mage_Sales_Model_Order{
	public function getShippingDescription(){
		$desc = parent::getShippingDescription();
		$orderId = $this->getId();
		$storepickup = Mage::getModel('storepickup/storepickup')->getStorePickUpByOrderId($orderId);
		if(count($storepickup))
		{
			$desc .= ' , Store Name : '.$storepickup['title']. ' , ';
			if(isset($storepickup['time']))
			{
				$desc .= 'Pickup Time : '.$storepickup['time'] .' ';
			}
		}
		return $desc;
	}
	function getStorePickUpAddress()
	{
		$pickupAddress = array();
		$orderId = $this->getId();
		$pickUpOrder = Mage::getModel('storepickup/pickuporder')->getCollection()
				->addFieldToFilter('pickup_order_id',$orderId)
				->getFirstItem();
		if($pickUpOrder->getId())
		{
			$pickupId = $pickUpOrder->getData('pickup_id');
			$objstorepickup = Mage::getModel('storepickup/storepickup')->load($pickupId);
			$storeName = $objstorepickup->getData('title');
			$storeAddress = $objstorepickup->getData('address');
			$storeCZ = $objstorepickup->getData('city'). ', '.$objstorepickup->getData('zipcode');
			$storeCountry = Mage::helper('storepickup')->getCountryByCode($objstorepickup->getData('country'));
			$tell = Mage::helper('storepickup')->__('T: ').$objstorepickup->getData('phone_number');
			$pickupAddress = array($storeName,$storeAddress,$storeCZ,$storeCountry,$tell);
		}
		return $pickupAddress;
	}
	public function sendNewOrderEmail()
    {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
        } catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
		$storeAddress = $this->getStorePickUpAddress();
		$customShiipingAddress = '';
		if(count($storeAddress))
		{
			$customShiipingAddress = implode('<br/>',$storeAddress);
		}
		else
		{
			$customShiipingAddress	= $this->getShippingAddress()->format('html');
		}
        $mailer->setTemplateParams(array(
                'order'        => $this,
                'billing'      => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml,
				'store_shipping_address' => $customShiipingAddress
            )
        );
        $mailer->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }
}