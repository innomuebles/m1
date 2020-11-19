<?php

class TM_Cache_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tmcacheLogGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _filterByCrawler($collection)
    {
        $collection->addFieldToFilter('crawler_id', 0);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tmcache/log')->getCollection();
        $this->_filterByCrawler($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'index'  => 'entity_id',
            'width' => '50px',
            'type'  => 'number'
        ));

        $this->addColumn('full_action_name', array(
            'header' => Mage::helper('tmcache')->__('Full Action Name'),
            'index'  => 'full_action_name',
            'width'   => '160px'
        ));

        $this->addColumn('request', array(
            'header'   => Mage::helper('adminhtml')->__('Request Path'),
            'index'    => 'params',
            'renderer' => 'tmcache/adminhtml_log_grid_renderer_request',
            'sortable' => false,
            'width'    => '200px'
        ));

        // $this->addColumn('theme', array(
        //     'header'   => Mage::helper('tmcache')->__('Theme'),
        //     'index'    => 'params',
        //     'renderer' => 'tmcache/adminhtml_log_grid_renderer_theme',
        //     'sortable' => false,
        //     'filter'   => false
        // ));

        $renderer = $this->getLayout()
            ->createBlock('tmcache/adminhtml_log_grid_renderer_params')
            ->setColumnsToUnset(array(
                'request_params',
                'request_uri'
            ));
        $this->addColumn('params', array(
            'header'   => Mage::helper('tmcache')->__('Additional Parameters'),
            'index'    => 'params',
            'sortable' => false,
            'renderer' => $renderer
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => false,
                'store_view'    => true,
                'sortable'      => false
            ));
        }

        $groups = Mage::getResourceModel('customer/group_collection')
            ->load()
            ->toOptionHash();
        $this->addColumn('customer_group_id', array(
            'header'  => Mage::helper('customer')->__('Group'),
            'width'   => '120',
            'index'   => 'customer_group_id',
            'type'    => 'options',
            'options' => $groups
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('reports')->__('Created At'),
            'index'  => 'created_at',
            'type'   => 'datetime'
        ));

        // $crawlers = Mage::getResourceModel('tmcrawler/crawler_collection')
        //     ->load()
        //     ->toOptionHash();
        // $this->addColumn('crawler_id', array(
        //     'header'  => Mage::helper('tmcrawler')->__('Crawler'),
        //     'width'   => '120',
        //     'index'   => 'crawler_id',
        //     'type'    => 'options',
        //     'options' =>
        //         array(
        //             0 => Mage::helper('tmcrawler')->__('NOT A CRAWLER')
        //         ) +
        //         $crawlers
        // ));

        $this->addColumn('result', array(
            'header'  => Mage::helper('tmcache')->__('Result'),
            'index'   => 'is_hit',
            'type'    => 'options',
            'width'   => '60px',
            'options' => array(
                0 => Mage::helper('tmcache')->__('Miss'),
                1 => Mage::helper('tmcache')->__('Hit')
            )
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
