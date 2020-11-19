<?php

class TM_BotProtection_Model_Resource_Pending_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('tm_botprotection/pending');
    }

    /**
     * Filter collection by Ip
     * @param mixed $ip (can be packed or unpacked address)
     */
    public function addIpFilter($ip)
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
        $this->getSelect()
            ->where(
                'ip = 0x'.$ipPacked
            );
        return $this;
    }
}
