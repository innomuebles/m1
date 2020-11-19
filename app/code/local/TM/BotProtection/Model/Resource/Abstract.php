<?php

abstract class TM_BotProtection_Model_Resource_Abstract
    extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Process list item data before saving
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /**
         * Prepare IP addresses to save
         */
        $helper = Mage::helper('tm_botprotection');
        if ($object->getData('is_ip_range')) {
            if ($object->hasData('ip_from_unpacked')) {
                $ip_from_packed = $helper->packIp($object->getData('ip_from_unpacked'));
                $object->setData('ip_from', $ip_from_packed);
            }
            if ($object->hasData('ip_to_unpacked')) {
                $ip_to_packed = $helper->packIp($object->getData('ip_to_unpacked'));
                $object->setData('ip_to', $ip_to_packed);
            }
        } else {
            if ($object->hasData('ip_unpacked')) {
                $ip_packed = $helper->packIp($object->getData('ip_unpacked'));
                $object->setData('ip_from', $ip_packed);
                $object->setData('ip_to', $ip_packed);
                // NCS for pending list table
                $object->setData('ip', $ip_packed);
            }
        }
        $currentTime = Mage::app()->getLocale()->date()
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        /**
         * Set date of creation
         */
        if (!$object->getId()) {
            $object->setData('create_time', $currentTime);
        }

        /**
         * Set date of last modification
         */
        $object->setData('update_time', $currentTime);
        /**
         * Remove useless leading and trailing spaces
         */
        $object->setData('crawler_name', trim($object->getData('crawler_name')));

        return parent::_beforeSave($object);
    }

    /**
     * Perform operations after object load
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getItemId()) {
            /**
             * CHECK IF RULE IS SINGLE IP OR RANGE
             */
            $helper = Mage::helper('tm_botprotection');
            if ($object->getData('ip_from') == $object->getData('ip_to')) {
                // this object containes rule for single ip
                // NCS for pending list table
                $ip_packed = is_null($object->getData('ip_from')) ?
                    $object->getData('ip') : $object->getData('ip_from');
                $ip_unpacked = $helper->unpackIp($ip_packed);
                $object->setData('ip_unpacked', $ip_unpacked);
                // it is not ip range
                $object->setData('is_ip_range', 0);
                $object->setData('ip_range_unpacked', $ip_unpacked);
            } else {
                // this object containes rule for ip range
                $ip_from_unpacked = $helper->unpackIp($object->getData('ip_from'));
                $ip_to_unpacked = $helper->unpackIp($object->getData('ip_to'));
                $object->setData('ip_from_unpacked', $ip_from_unpacked);
                $object->setData('ip_to_unpacked', $ip_to_unpacked);
                // it is ip range
                $object->setData('is_ip_range', 1);
                $ip_from_unpacked .= ' - '.$ip_to_unpacked;
                $object->setData('ip_range_unpacked', $ip_from_unpacked);
            }
        }
        return parent::_afterLoad($object);
    }
}
