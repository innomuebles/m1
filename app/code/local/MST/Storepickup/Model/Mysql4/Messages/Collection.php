<?php
class MST_Storepickup_Model_Mysql4_Messages_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/messages');
    }
}

?>