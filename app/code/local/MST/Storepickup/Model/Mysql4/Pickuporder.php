<?php
class MST_Storepickup_Model_Mysql4_Pickuporder extends Mage_Core_Model_Mysql4_Abstract
{
    function _construct()
    {
        $this->_init('storepickup/pickuporder','id');
    }
}
?>