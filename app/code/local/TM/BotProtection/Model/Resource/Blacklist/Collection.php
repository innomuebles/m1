<?php

class TM_BotProtection_Model_Resource_Blacklist_Collection
    extends TM_BotProtection_Model_Resource_Abstract_Collection
{

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/blacklist');
        $this->_map['fields']['item_id'] = 'main_table.item_id';
    }

}
