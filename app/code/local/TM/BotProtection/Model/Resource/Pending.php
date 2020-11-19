<?php

class TM_BotProtection_Model_Resource_Pending
    extends TM_BotProtection_Model_Resource_Abstract
{

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/pending', 'item_id');
    }
    
}
