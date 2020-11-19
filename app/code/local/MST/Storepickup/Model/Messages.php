<?php
class MST_Storepickup_Model_Messages extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/messages');
    }
}
?>