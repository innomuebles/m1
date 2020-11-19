<?php

/**
 * PACKT Storepickup Collection Model specialized for MySQL4
 *
 * @category   PACKT
 * @package    MST_Storepickup

 */
class MST_Storepickup_Model_Mysql4_Storepickup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/storepickup');
    }

}