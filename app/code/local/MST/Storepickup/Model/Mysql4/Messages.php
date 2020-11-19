<?php
class MST_Storepickup_Model_Mysql4_Messages extends Mage_Core_Model_Mysql4_Abstract
{
    function _construct()
    {
        $this->_init('storepickup/messages','message_id');
    }
}
?>