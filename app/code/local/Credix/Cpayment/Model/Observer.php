<?php
class Credix_Cpayment_Model_Observer extends Varien_Event_Observer
{    
    function credixCheckout(Varien_Event_Observer $event)
    {           
       // Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/onepage/success"));        
    }    
}

?>
