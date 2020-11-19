<?php

class TM_BotProtection_Model_Resource_Whitelist
    extends TM_BotProtection_Model_Resource_Abstract
{

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/whitelist', 'item_id');
    }
    
}
