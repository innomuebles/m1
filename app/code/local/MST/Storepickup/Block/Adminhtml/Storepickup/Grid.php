<?php 
class MST_Storepickup_Block_Adminhtml_Storepickup_Grid extends MST_Storepickup_Block_Adminhtml_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('storepickupGrid');
        $this->setDefaultSort('cats_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('storepickup/storepickup')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('storepickup_id', array(
            'header' => Mage::helper('storepickup')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'storepickup_id',
        ));
		$this->addColumn('title', array(
            'header' => Mage::helper('storepickup')->__('Store Name'),
            'align' => 'left',
            'index' => 'title',
        ));
		$this->addColumn('address', array(
            'header' => Mage::helper('storepickup')->__('Store Address'),
            'align' => 'left',
            'index' => 'address',
        ));
		$this->addColumn('city', array(
            'header' => Mage::helper('storepickup')->__('City'),
            'align' => 'left',
            'index' => 'city',
        ));
		$this->addColumn('zipcode', array(
            'header' => Mage::helper('storepickup')->__('Zip Code'),
            'align' => 'left',
            'index' => 'zipcode',
        ));
		
		$this->addColumn('country', array(
				'header'    => Mage::helper('storepickup')->__('Country'),
				'width'     => '110px',
				'index'     => 'country',
				'type'		=> 'options',
				'options'   => Mage::helper('storepickup')->getListCountry(),
		));
		
		$this->addColumn('position', array(
            'header' => Mage::helper('storepickup')->__('Position'),
            'align' => 'left',
            'index' => 'position',
        ));
		
        $this->addColumn('storepickup_status', array(
            'header' => Mage::helper('storepickup')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'storepickup_status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header' => Mage::helper('storepickup')->__('Action'),
                'width' => '100',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('storepickup')->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('storepickup')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('storepickup')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('storepickup_id');
        $this->getMassactionBlock()->setFormFieldName('storepickup');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('storepickup')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('storepickup')->__('Are you sure?')
        ));
        $status = Mage::getSingleton('storepickup/status')->getOptionArray();
        array_unshift($status, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('storepickup_status', array(
            'label' => Mage::helper('storepickup')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('storepickup')->__('Status'),
                    'values' => $status
                )
            )
        ));
        return $this;
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}