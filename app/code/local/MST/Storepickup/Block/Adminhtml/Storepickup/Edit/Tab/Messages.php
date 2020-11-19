<?php
class MST_Storepickup_Block_Adminhtml_Storepickup_Edit_Tab_Messages extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct() {
        parent::__construct();
        $this->setId('storepickupMessages');
        $this->setDefaultSort('message_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        
    }

    protected function _prepareCollection() {
        $pickupId = $this->getRequest()->getParam('id');
        if ($pickupId) {
            $collection = Mage::getModel('storepickup/messages')->getCollection()
            ->addFieldToFilter('pickup_id',$pickupId);
            $this->setCollection($collection);
        }
        
        return parent::_prepareCollection();
    }
    protected function _addColumnFilterToCollection($column) {
        if ($this->getCollection()) {
            if ($column->getId() == 'in_message') {
                $messages = $this->_getSelectedMessage();
                if (empty($messages)) {
                    $messages = 0;
                }
                if ($column->getFilter()->getValue()) {
                    $this->getCollection()->addFieldToFilter('message_id', array('in' => $messages));
                } else {
                    if ($messages) {
                         $this->getCollection()->addFieldToFilter('message_id', array('nin' => $messages));                       
                    }
                }
            } else {
                parent::_addColumnFilterToCollection($column);
            }
          
        }
        return $this;;
    }
    protected function _prepareColumns() {
         $this->addColumn('in_message', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'values' => $this->_getSelectedMessage(),
            'align' => 'center',
            'index' => 'message_id'
        ));
        $this->addColumn('message_id', array(
            'header' => Mage::helper('storepickup')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'message_id',
        ));
        $this->addColumn('pickup_contact_name', array(
            'header' => Mage::helper('storepickup')->__('Name'),
            'align' => 'left',
            'index' => 'pickup_contact_name',
        ));
         $this->addColumn('pickup_contact_email', array(
            'header' => Mage::helper('storepickup')->__('Email'),
            'align' => 'left',
            'index' => 'pickup_contact_email',
        ));
         $this->addColumn('pickup_contact_message', array(
            'header' => Mage::helper('storepickup')->__('Message'),
            'align' => 'left',
            'index' => 'pickup_contact_message',
            'renderer' => 'MST_Storepickup_Block_Adminhtml_Grid_Renderer_Message'
        ));
          $this->addColumn('pickup_contact_at', array(
            'header' => Mage::helper('storepickup')->__('Contact At'),
            'align' => 'left',
            'index' => 'pickup_contact_at',
        ));
           $this->addColumn('action',
                array(
                    'header' => Mage::helper('storepickup')->__('Action'),
                    'width' => '80',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => Mage::helper('storepickup')->__('View'),
                            'url' => array('base' => 'storepickup/adminhtml_message/edit'),
                            'field' => 'id'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'is_system' => true,
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('storepickup/adminhtml_storepickup/loadmessages', array('_current' => true));
    }
   /*  public function getRowUrl() {
        return '#';
    } */
    function _getSelectedMessage()
    {
    
        $messageIds = array();
        $messageIds = $this->getRequest()->getPost('selected_messages'); 
        return $messageIds;
    }
}
?>