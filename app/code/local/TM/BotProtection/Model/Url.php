<?php

class TM_BotProtection_Model_Url extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/url', 'url_id');
    }

}
