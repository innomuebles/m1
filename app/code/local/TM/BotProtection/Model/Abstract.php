<?php

abstract class TM_BotProtection_Model_Abstract extends Mage_Core_Model_Abstract
{

    /**
     * Find item in bot list for IP and Crawler
     */
    public function findItem($ipPacked, $crawlerName)
    {
        $listCollection = $this->getCollection();
        $listCollection
            ->addFieldToFilter('status', Mage_Cms_Model_Page::STATUS_ENABLED);
        $listCollection->addIpFilter($ipPacked);
        $listCollection->getSelect()
            ->where(
                ' crawler_name = ? '
                .'OR '
                .'crawler_name IS NULL '
                ,
                $crawlerName
            );
        $listCollection->getSelect()
            ->order('crawler_name DESC');
        $listCollection->getSelect()
            ->order('ip_from DESC');
        return $listCollection->getFirstItem();
    }

}
