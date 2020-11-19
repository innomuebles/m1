<?php

abstract class TM_BotProtection_Block_Adminhtml_Abstract_List_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $_listName = '';

    /**# code...
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('item_id' => $row->getId()));
    }

    public function getAvailableDetectedBy()
    {
        return Mage::getSingleton(
            'tm_botprotection/system_config_source_detectedby'
            )->toArray();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tm_botprotection/' . $this->_listName)
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        $aclPath = 'templates_master/tm_botprotection/'
            . $this->_listName
            . '/'
            . $action ;
        return Mage::getSingleton('admin/session')->isAllowed($aclPath);
    }

    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('item_id');
        $this->getMassactionBlock()->setFormFieldName('items');

        if ($this->_isAllowedAction('save')) {
            $this->getMassactionBlock()->addItem('disable', array(
                'label'=> Mage::helper('catalog')->__('Disable'),
                'url' => $this->getUrl('*/*/massDisable')
            ));
            $this->getMassactionBlock()->addItem('enable', array(
                'label'=> Mage::helper('catalog')->__('Enable'),
                'url' => $this->getUrl('*/*/massEnable')
            ));
        }

        if ($this->_isAllowedAction('delete')) {
            $this->getMassactionBlock()->addItem('delete', array(
                'label'=> Mage::helper('catalog')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => $this->__(
                    'Deleted items cannot be restored. Are you sure?'
                )
            ));
        }

        return $this;
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterIpCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addIpFilter($value);
    }


}
