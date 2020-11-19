<?php
/**
 *  Adminhtml TM BotProtection Whitelist grid
 */
class TM_BotProtection_Block_Adminhtml_Whitelist_Grid
    extends TM_BotProtection_Block_Adminhtml_Abstract_List_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('tmBotprotectionWhitelistGrid');
        $this->setDefaultSort('identifier');
        $this->setDefaultDir('ASC');
        $this->_listName = 'whitelist';
    }

    protected function _prepareColumns()
    {

        $helper = Mage::helper('tm_botprotection');

        $this->addColumn('item_id', array(
            'header'    => $helper->__('Item ID'),
            'align'     => 'right',
            'index'     => 'item_id',
            'width'     => '50px'
        ));

        $this->addColumn('ip_range_unpacked', array(
            'header'    => $helper->__('IP'),
            'align'     => 'left',
            'index'     => 'ip_range_unpacked',
            'width'     => '220px',
            'filter_condition_callback'
                        => array($this, '_filterIpCondition')
        ));

        $this->addColumn('crawler_name', array(
            'header'    => $helper->__('Crawler/Bot'),
            'align'     => 'left',
            'index'     => 'crawler_name'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cms/page')->getAvailableStatuses()
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $this->addColumn('update_time', array(
            'header'    => $helper->__('Modified at'),
            'index'     => 'update_time',
            'type'      => 'date', // DO NOT use 'datetime'. It has issues with timezone
            'format'    => $dateFormatIso
        ));

        $this->addColumn('create_time', array(
            'header'    => $helper->__('Created at'),
            'index'     => 'create_time',
            'type'      => 'date', // DO NOT use 'datetime'. It has issues with timezone
            'format'    => $dateFormatIso
        ));

        return parent::_prepareColumns();
    }

}
