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
 * API2 class for orders (admin)
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Orderhive_Restapi_Model_Api2_Order_Rest_Admin_V1 extends Orderhive_Restapi_Model_Api2_Order_Rest
{
	protected function _update(array $data)
    {
    	$order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('id'));
    	$items = $order->getItemsCollection();
    	if(!isset($data['status']) || empty($data['status']))
    		return false;

    	if($data['status'] == 'shipped' && !empty($data['items']))
    	{
    		$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($data['items']);
    		$arrTracking = array(
    			'carrier_code' => $order->getShippingCarrier()->getCarrierCode(),
                'title' => isset($data['shipping_description']) ? $data['shipping_description'] : $order->getShippingCarrier()->getConfigData('title'),
                'number' => isset($data['tracking_number']) ? $data['tracking_number'] : Null
            );
            $track = Mage::getModel('sales/order_shipment_track')->addData($arrTracking);
            $shipment->addTrack($track);
            $shipment->register();
            $shipment->sendEmail(true)->setEmailSent(true)->save();
            $order->setIsInProcess(true);
            $transactionSave = Mage::getModel('core/resource_transaction')->addObject($shipment)->addObject($shipment->getOrder())->save();
            $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
    	}
    	elseif($data['status'] == 'complete' && !empty($data['items']))
    	{
    		$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($data['items']);
    		$amount = $invoice->getGrandTotal();
            $invoice->register()->pay();
            $invoice->getOrder()->setIsInProcess(true);
            $history = $invoice->getOrder()->addStatusHistoryComment(
                    'Partial amount of $' . $amount . ' captured automatically.', false
                );
            $history->setIsCustomerNotified(true);
            $order->save();
            Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder())->save();
            $invoice->save();
            $invoice->sendEmail(true, '');
    	}
    	elseif($data['status'] == 'canceled')
    	{
    		$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
    	}
    }
}
