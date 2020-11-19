<?php
if (Mage::helper('core')->isModuleOutputEnabled('TM_CDN')) {
    class TM_Cache_Block_Page_Html_HeaderAbstract extends TM_CDN_Block_Html_Header
    {

    }
} else {
    class TM_Cache_Block_Page_Html_HeaderAbstract extends Mage_Page_Block_Html_Header
    {

    }
}