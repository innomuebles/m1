<?php

class TM_BotProtection_Model_Resource_Url
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        // die('You are in model resource constructor');
        $this->_init('log/url_table', 'url_id');
    }
}
