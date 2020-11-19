<?php
class MST_Storepickup_Block_Adminhtml_Storepickup_Edit_Tab_Gridorders extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct() {
        parent::__construct();
        $this->setId('storepickupGridorders');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        
    }
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }
     protected function _prepareCollection()
    {
        $pickupId = $this->getRequest()->getParam('id');
        if($pickupId > 0){
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $collection->getSelect()->join(array('order_pickup' =>'storepickup_orders'),
         "main_table.entity_id = order_pickup.pickup_order_id
          AND order_pickup.pickup_id = ".$pickupId. "
         "); 
         $this->setCollection($collection);
        }
    
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }
      
        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));
//
//        $this->addColumn('pickup_name', array(
//            'header' => Mage::helper('sales')->__('Ship to name'),
//            'index' => 'pickup_name',
//        ));
//
        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'adminhtml/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        return parent::_prepareColumns();
    }

  public function getGridUrl() {
        return $this->getUrl('storepickup/adminhtml_storepickup/loadorder', array('_current' => true));
    }
    public function getRowUrl() {
        return '#';
    }
}
?>