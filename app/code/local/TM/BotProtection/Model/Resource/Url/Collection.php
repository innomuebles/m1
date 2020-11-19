<?php

class TM_BotProtection_Model_Resource_Url_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Define resource model
     *
     */
    protected function _construct()
    {
        $this->_init('log/url_table');
        // $this->_map['fields']['url_id'] = 'main_table.url_id';
    }

    public function joinUrlInfo($cols = '*')
    {
        return $this->join(
            array('info' => 'log/url_info_table'),
            'main_table.url_id = info.url_id',
            $cols
        );
    }

    public function joinVisitor($cols = '*')
    {
        return $this->join(
            array('v' => 'log/visitor'),
            'main_table.visitor_id = v.visitor_id',
            $cols
        );
    }

    public function joinVisitorInfo($cols = '*')
    {
        return $this->join(
            array('v_info' => 'log/visitor_info'),
            'main_table.visitor_id = v_info.visitor_id',
            $cols
        );
    }

    public function addVisitTimeFilter($from, $to)
    {
        return $this
            ->addFieldToFilter(
                "main_table.visit_time",
                array("gteq" => $from->toString(Varien_Date::DATETIME_INTERNAL_FORMAT))
            )
            ->addFieldToFilter(
                "main_table.visit_time",
                array("lteq" => $to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT))
            );
    }

    public function addVisitorIpFilter($ip)
    {
        if (strlen($ip) != 4) {
            // assuming that it is not a binary string
            $ipPacked = bin2hex(Mage::helper('tm_botprotection')->packIp($ip));
            if (!$ipPacked) {
                $ipPacked = '00000000';
            }
        } else {
            $ipPacked = bin2hex($ip);
        }
        return $this->getSelect()->where(
            'v_info.remote_addr = 0x'.$ipPacked
        );
    }

    public function addStoreFilter($store_id)
    {
        return $this->addFieldToFilter(
            "v.store_id",
            array("eq" => $store_id)
        );
    }

    /** 
     * Override varien method to improve speed
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);

        $countSelect->columns('COUNT(main_table.url_id)');

        return $countSelect;
    }
}
