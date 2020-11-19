<?php
/**
 *  Adminhtml TM BotProtection Pending grid
 */
class TM_BotProtection_Block_Adminhtml_Pending_Grid
    extends TM_BotProtection_Block_Adminhtml_Abstract_List_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('tmBotprotectionPendingGrid');
        $this->setDefaultSort('identifier');
        $this->setDefaultDir('ASC');
        $this->_listName = 'pending';
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

        $this->addColumn('ip_unpacked', array(
            'header'    => $helper->__('IP'),
            'align'     => 'left',
            'index'     => 'ip_unpacked',
            'width'     => '220px',
            'filter_condition_callback'
                        => array($this, '_filterIpCondition')
        ));

        $this->addColumn('crawler_name', array(
            'header'    => $helper->__('Crawler/Bot'),
            'align'     => 'left',
            'index'     => 'crawler_name'
        ));

        $this->addColumn('user_agent', array(
            'header'    => $helper->__('User Agent'),
            'align'     => 'left',
            'index'     => 'user_agent'
        ));

        $this->addColumn('ask_confirm_human', array(
            'header'    => $helper->__('Req. Confirm'),
            'index'     => 'ask_confirm_human',
            'align' => 'center',
            'type' => 'options',
            'options'   => Mage::getModel('adminhtml/system_config_source_yesno')->toArray()
        ));

        $this->addColumn('confirmed_human', array(
            'header'    => $helper->__('Human'),
            'index'     => 'confirmed_human',
            'align' => 'center',
            'type' => 'options',
            'options'   => Mage::getModel('adminhtml/system_config_source_yesno')->toArray()
        ));

        $this->addColumn('failed_attempts', array(
            'header'    => $helper->__('Failed Attempts'),
            'align'     => 'center',
            'index'     => 'failed_attempts'
        ));

        $this->addColumn('detected_by', array(
            'header'    => $helper->__('Detected by'),
            'type'      => 'options',
            'index'     => 'detected_by',
            'options'   => $this->getAvailableDetectedBy()
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
