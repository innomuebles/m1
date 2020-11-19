<?php
/**
 * Cleanup Script for Magento 1 Shops
 * for invalid users - see README.md
 *
 * inspired by:
 * https://magento.stackexchange.com/questions/71910/find-and-clean-up-unconfirmed-customer-accounts
 * https://inchoo.net/magento/delete-spam-customer-accounts-magento/
 * https://inchoo.net/magento/programming-magento/how-to-delete-magento-product-from-frontend-template-code-from-view-files/
 *
 * infoworxx GmbH 2019, Sebastian Lemke
 * WITHOUT ANY WARRANTY!
 * USE ON YOUR OWN RISK!
 * MAKE A BACKUP BEFORE USING!
 */

ini_set("display_errors", true);
define('MAGENTO_ROOT', getcwd());

//require MAGENTO_ROOT . '/app/Mage.php';
require_once(__DIR__ . '/app/Mage.php');

Mage::app();
Mage::register('isSecureArea', true);
umask(0); 
 //Set memory limit
ini_set("memory_limit","3072M");

$customers = Mage::getModel("customer/customer")
			->getCollection()
			->addAttributeToFilter('email', array('like' => '%@qq.com'))
			->setPageSize(300);

foreach ($customers as $customer) {
    $customer->delete();
}
//echo 'Next&nbsp;'.$customers->count();
//exit("customers deleted");

//exit;