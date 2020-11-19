<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 class for orders
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Orderhive_Restapi_Model_Api2_Order extends Mage_Api2_Model_Resource
{
    /**#@+
     * Parameters' names in config with special ACL meaning
     */
    const PARAM_GIFT_MESSAGE   = '_gift_message';
    const PARAM_ORDER_COMMENTS = '_order_comments';
    const PARAM_PAYMENT_METHOD = '_payment_method';
    const PARAM_TAX_NAME       = '_tax_name';
    const PARAM_TAX_RATE       = '_tax_rate';
    /**#@-*/

    /**
     * Add gift message info to select
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return Mage_Sales_Model_Api2_Order
     */
    protected function _addGiftMessageInfo(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        $collection->getSelect()->joinLeft(
            array('gift_message' => $collection->getTable('giftmessage/message')),
            'main_table.gift_message_id = gift_message.gift_message_id',
            array(
                'gift_message_from' => 'gift_message.sender',
                'gift_message_to'   => 'gift_message.recipient',
                'gift_message_body' => 'gift_message.message'
            )
        );

        return $this;
    }

    /**
     * Add order payment method field to select
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return Mage_Sales_Model_Api2_Order
     */
    protected function _addPaymentMethodInfo(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        $collection->getSelect()->joinLeft(
            array('payment_method' => $collection->getTable('sales/order_payment')),
            'main_table.entity_id = payment_method.parent_id',
            array('payment_method' => 'payment_method.method')
        );

        return $this;
    }

    /**
     * Add order tax information to select
     *
     * @param Mage_Sales_Model_Resource_Order_Collection $collection
     * @return Mage_Sales_Model_Api2_Order
     */
    protected function _addTaxInfo(Mage_Sales_Model_Resource_Order_Collection $collection)
    {
        $taxInfoFields = array();

        if ($this->_isTaxNameAllowed()) {
            $taxInfoFields['tax_name'] = 'order_tax.title';
        }
        if ($this->_isTaxRateAllowed()) {
            $taxInfoFields['tax_rate'] = 'order_tax.percent';
        }
        if ($taxInfoFields) {
            $collection->getSelect()->joinLeft(
                array('order_tax' => $collection->getTable('sales/order_tax')),
                'main_table.entity_id = order_tax.order_id',
                $taxInfoFields
            );
            $collection->getSelect()->group('main_table.entity_id');
        }
        return $this;
    }

    /**
     * Retrieve a list or orders' addresses in a form of [order ID => array of addresses, ...]
     *
     * @param array $orderIds Orders identifiers
     * @return array
     */
    protected function _getAddresses(array $orderIds)
    {
        $addresses = array();

        if ($this->_isSubCallAllowed('order_address')) {
            /** @var $addressesFilter Mage_Api2_Model_Acl_Filter */
            $addressesFilter = $this->_getSubModel('order_address', array())->getFilter();
            // do addresses request if at least one attribute allowed
            if ($addressesFilter->getAllowedAttributes()) {
                /* @var $collection Mage_Sales_Model_Resource_Order_Address_Collection */
                $collection = Mage::getResourceModel('sales/order_address_collection');

                $collection->addAttributeToFilter('parent_id', $orderIds);

                foreach ($collection->getItems() as $item) {
                    $addresses[$item->getParentId()][] = $addressesFilter->out($item->toArray());
                }
            }
        }
        return $addresses;
    }

    /**
     * Retrieve collection instance for orders list
     *
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel('sales/order_collection');

        $this->_applyCollectionModifiers($collection);

        return $collection;
    }

    /**
     * Retrieve collection instance for single order
     *
     * @param int $orderId Order identifier
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    protected function _getCollectionForSingleRetrieve($orderId)
    {
        /** @var $collection Mage_Sales_Model_Resource_Order_Collection */
        $collection = Mage::getResourceModel('sales/order_collection');

        return $collection->addFieldToFilter($collection->getResource()->getIdFieldName(), $orderId);
    }

    /**
     * Retrieve a list or orders' comments in a form of [order ID => array of comments, ...]
     *
     * @param array $orderIds Orders' identifiers
     * @return array
     */
    protected function _getComments(array $orderIds)
    {
        $comments = array();

        if ($this->_isOrderCommentsAllowed() && $this->_isSubCallAllowed('order_comment')) {
            /** @var $commentsFilter Mage_Api2_Model_Acl_Filter */
            $commentsFilter = $this->_getSubModel('order_comment', array())->getFilter();
            // do comments request if at least one attribute allowed
            if ($commentsFilter->getAllowedAttributes()) {
                foreach ($this->_getCommentsCollection($orderIds)->getItems() as $item) {
                    $comments[$item->getParentId()][] = $commentsFilter->out($item->toArray());
                }
            }
        }
        return $comments;
    }

    /**
     * Prepare and return order comments collection
     *
     * @param array $orderIds Orders' identifiers
     * @return Mage_Sales_Model_Resource_Order_Status_History_Collection|Object
     */
    protected function _getCommentsCollection(array $orderIds)
    {
        /* @var $collection Mage_Sales_Model_Resource_Order_Status_History_Collection */
        $collection = Mage::getResourceModel('sales/order_status_history_collection');
        $collection->setOrderFilter($orderIds);

        return $collection;
    }

    /**
     * Retrieve a list or orders' items in a form of [order ID => array of items, ...]
     *
     * @param array $orderIds Orders identifiers
     * @return array
     */
    protected function _getItems(array $orderIds)
    {
        $items = array();

        if ($this->_isSubCallAllowed('order_item')) {
            /** @var $itemsFilter Mage_Api2_Model_Acl_Filter */
            $itemsFilter = $this->_getSubModel('order_item', array())->getFilter();
            // do items request if at least one attribute allowed
            if ($itemsFilter->getAllowedAttributes()) {
                /* @var $collection Mage_Sales_Model_Resource_Order_Item_Collection */
                $collection = Mage::getResourceModel('sales/order_item_collection');

                $collection->addAttributeToFilter('order_id', $orderIds);

                foreach ($collection->getItems() as $item) {
                    $items[$item->getOrderId()][] = $itemsFilter->out($item->toArray());
                }
            }
        }
        return $items;
    }

    /**
     * Check gift messages information is allowed
     *
     * @return bool
     */
    public function _isGiftMessageAllowed()
    {
        return in_array(self::PARAM_GIFT_MESSAGE, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Check order comments information is allowed
     *
     * @return bool
     */
    public function _isOrderCommentsAllowed()
    {
        return in_array(self::PARAM_ORDER_COMMENTS, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Check payment method information is allowed
     *
     * @return bool
     */
    public function _isPaymentMethodAllowed()
    {
        return in_array(self::PARAM_PAYMENT_METHOD, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Check tax name information is allowed
     *
     * @return bool
     */
    public function _isTaxNameAllowed()
    {
        return in_array(self::PARAM_TAX_NAME, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Check tax rate information is allowed
     *
     * @return bool
     */
    public function _isTaxRateAllowed()
    {
        return in_array(self::PARAM_TAX_RATE, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Get orders list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $collection = $this->_getCollectionForRetrieve();

        $curPage = $this->getRequest()->getPageNumber();
        $lastPageNumber = $collection->getLastPageNumber();
        if(!empty($curPage) && $curPage > $lastPageNumber)
            return array();

        if ($this->_isPaymentMethodAllowed()) {
            $this->_addPaymentMethodInfo($collection);
        }
        if ($this->_isGiftMessageAllowed()) {
            $this->_addGiftMessageInfo($collection);
        }
        $this->_addTaxInfo($collection);

        $result = $this->_prepareData($collection);
        
        return $result;
    }

    protected function _prepareData($collection)
    {
        $result = array();
        $cnt = 0;

        $tableName = Mage::getSingleton('core/resource')->getTableName('core_config_data');
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $query1 = "SELECT value FROM  ".$tableName." WHERE  `path` LIKE  'tax/calculation/price_includes_tax'";
        $result1 = $readConnection->fetchAll($query1);
        if($result1[0]['value'] == 1)
            $tax_config['price'] = 'inward';
        else
            $tax_config['price'] = 'outward';

        $query2 = "SELECT value FROM  ".$tableName." WHERE  `path` LIKE  'tax/calculation/shipping_includes_tax'";
        $result2 = $readConnection->fetchAll($query2);
        if($result2[0]['value'] == 1)
            $tax_config['shipping'] = 'inward';
        else
            $tax_config['shipping'] = 'outward';

        foreach ($collection as $order) 
        {
            $shipCnt = 0;
            $result[$cnt] = $order->getData();
            $result[$cnt]['tax_type'] = $tax_config['price'];
            $result[$cnt]['shipping_tax_type'] = $tax_config['shipping'];
			if($order->getShippingAddress() != Null)
				$result[$cnt]['shipping_address'] = $order->getShippingAddress()->getData();
			else
				$result[$cnt]['shipping_address'] = Null;
			if($order->getBillingAddress() != Null)
				$result[$cnt]['billing_address'] = $order->getBillingAddress()->getData();
			else
				$result[$cnt]['billing_address'] = Null;
            foreach ($order->getAllItems() as $key => $item) 
            {
                $itemData = $item->getData();
                if($itemData['product_type'] == 'bundle')
                    $itemData['product_options'] = json_encode(unserialize($itemData['product_options']));
                if($itemData['product_type'] == 'configurable')
                    $tempArray = $itemData;
                if($itemData['product_type'] == 'simple' && $itemData['sku'] == $tempArray['sku'])
                {
                    $itemData['qty_canceled'] = ($itemData['qty_canceled'] <= 0) ? $tempArray['qty_canceled'] : $itemData['qty_canceled'];
                    $itemData['qty_invoiced'] = ($itemData['qty_invoiced'] <= 0) ? $tempArray['qty_invoiced'] : $itemData['qty_invoiced'];
                    $itemData['qty_refunded'] = ($itemData['qty_refunded'] <= 0) ? $tempArray['qty_refunded'] : $itemData['qty_refunded'];
                    $itemData['qty_shipped'] = ($itemData['qty_shipped'] <= 0) ? $tempArray['qty_shipped'] : $itemData['qty_shipped'];
                    $itemData['price'] = ($itemData['price'] <= 0) ? $tempArray['price'] : $itemData['price'];
                    $itemData['base_price'] = ($itemData['base_price'] <= 0) ? $tempArray['base_price'] : $itemData['base_price'];
                    $itemData['original_price'] = ($itemData['original_price'] <= 0) ? $tempArray['original_price'] : $itemData['original_price'];
                    $itemData['base_original_price'] = ($itemData['base_original_price'] <= 0) ? $tempArray['base_original_price'] : $itemData['base_original_price'];
                    $itemData['tax_percent'] = ($itemData['tax_percent'] <= 0) ? $tempArray['tax_percent'] : $itemData['tax_percent'];
                    $itemData['tax_amount'] = ($itemData['tax_amount'] <= 0) ? $tempArray['tax_amount'] : $itemData['tax_amount'];
                    $itemData['discount_percent'] = ($itemData['discount_percent'] <= 0) ? $tempArray['discount_percent'] : $itemData['discount_percent'];
                    $itemData['discount_amount'] = ($itemData['discount_amount'] <= 0) ? $tempArray['discount_amount'] : $itemData['discount_amount'];
                    $itemData['row_total'] = ($itemData['row_total'] <= 0) ? $tempArray['row_total'] : $itemData['row_total'];
                    $itemData['base_row_total'] = ($itemData['base_row_total'] <= 0) ? $tempArray['base_row_total'] : $itemData['base_row_total'];
                    $itemData['row_weight'] = ($itemData['row_weight'] <= 0) ? $tempArray['row_weight'] : $itemData['row_weight'];
                    $itemData['price_incl_tax'] = ($itemData['price_incl_tax'] <= 0) ? $tempArray['price_incl_tax'] : $itemData['price_incl_tax'];
                    $itemData['base_price_incl_tax'] = ($itemData['base_price_incl_tax'] == null) ? $tempArray['base_price_incl_tax'] : $itemData['base_price_incl_tax'];
                    $itemData['row_total_incl_tax'] = ($itemData['row_total_incl_tax'] == null) ? $tempArray['row_total_incl_tax'] : $itemData['row_total_incl_tax'];
                    $itemData['base_row_total_incl_tax'] = ($itemData['base_row_total_incl_tax'] == null) ? $tempArray['base_row_total_incl_tax'] : $itemData['base_row_total_incl_tax'];
                    $itemData['hidden_tax_amount'] = ($itemData['hidden_tax_amount'] == null) ? $tempArray['hidden_tax_amount'] : $itemData['hidden_tax_amount'];
                    $itemData['base_hidden_tax_amount'] = ($itemData['base_hidden_tax_amount'] == null) ? $tempArray['base_hidden_tax_amount'] : $itemData['base_hidden_tax_amount'];
                    $itemData['base_weee_tax_applied_row_amnt'] = ($itemData['base_weee_tax_applied_row_amnt'] == null) ? $tempArray['base_weee_tax_applied_row_amnt'] : $itemData['base_weee_tax_applied_row_amnt'];
                    $itemData['base_weee_tax_applied_row_amount'] = ($itemData['base_weee_tax_applied_row_amount'] == null) ? $tempArray['base_weee_tax_applied_row_amount'] : $itemData['base_weee_tax_applied_row_amount'];
                    $tempArray = array();
                }
                $result[$cnt]['items'][$key] = $itemData;
            }
            $result[$cnt]['payment'] = $order->getPayment()->getData();
            foreach ($order->getAllStatusHistory() as $key => $history)
                $result[$cnt]['status_history'][$key] = $history->getData();
            foreach($order->getShipmentsCollection() as $key => $shipment)
            {
                $result[$cnt]['shipments'][$shipCnt] = $shipment->getData();
                foreach ($shipment->getAllItems() as $key1 => $item) 
                    $result[$cnt]['shipments'][$shipCnt]['items'][$key1] = $item->getData();
                foreach ($shipment->getAllTracks() as $key1 => $track)
                    $result[$cnt]['shipments'][$shipCnt]['tracks'][$key1] = $track->getData();
                $shipCnt++;
            }
            $cnt++;
        }
        return $result;
    }
}
