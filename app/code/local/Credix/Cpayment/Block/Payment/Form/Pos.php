<?php
class Credix_Cpayment_Block_Payment_Form_Pos extends Mage_Payment_Block_Form_Cc  
{  
    protected function _construct()  
    {  
        parent::_construct();      
        $this->setTemplate('cpayment/payment/form/pos.phtml'); 
    }  
    
    public function getBankAvailableTypes()  
    {  
        return Mage::getSingleton("cpayment/bank")->getCollection();  
    }  
}  

?>