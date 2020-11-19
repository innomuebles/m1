<?php

class TM_Cache_Block_Adminhtml_Usage_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tmcacheUsageGrid');
        $this->setDefaultSort('hit_count');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);

        // set the cache_id field for massaction column
        // @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Massaction::render()
        // @see Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Checkbox::render()
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->setMassactionBlockName('tmcache/adminhtml_widget_grid_massaction');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tmcache/log')->getCollection();
        $collection->getSelect()
            ->group('cache_id')
            ->columns(array(
                'hit_count'   => 'COUNT(entity_id) - 1',
                'last_access' => 'MAX(created_at)'
            ))
            ->where('cache_id <> ?', '')
            ->where('crawler_id = 0');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('full_action_name', array(
            'header' => Mage::helper('tmcache')->__('Full Action Name'),
            'index'  => 'full_action_name',
            'width'   => 160
        ));

        // $this->addColumn('cache_id', array(
        //     'header' => Mage::helper('catalog')->__('ID'),
        //     'index'  => 'cache_id',
        //     'width'  => 220
        // ));

        $this->addColumn('request', array(
            'header'   => Mage::helper('adminhtml')->__('Request Path'),
            'index'    => 'params',
            'renderer' => 'tmcache/adminhtml_log_grid_renderer_request',
            'sortable' => false,
            'width'    => 200
        ));

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
            'width'   => 120,
            'index'   => 'customer_group_id',
            'type'    => 'options',
            'options' => $groups
        ));

        $this->addColumn('last_access', array(
            'header' => Mage::helper('reports')->__('Last Access'),
            'index'  => 'last_access',
            'type'   => 'datetime'
        ));

        $this->addColumn('hit_count', array(
            'header'  => Mage::helper('tmcache')->__('Hit Count'),
            'index'   => 'hit_count',
            'type'    => 'number',
            'width'   => 60
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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cache_id');
        $this->getMassactionBlock()->setFormFieldName('cache_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('tmcache')->__('Delete Cache Records'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('adminhtml')->__('Are you sure?')
        ));

        return $this;
    }
}
