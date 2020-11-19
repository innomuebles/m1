<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'tm_botprotection/blacklist'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tm_botprotection/blacklist'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
        ), 'Id')
    ->addColumn('ip_from', Varien_Db_Ddl_Table::TYPE_VARBINARY, 16, array(
        ), 'IP from')
    ->addColumn('ip_to', Varien_Db_Ddl_Table::TYPE_VARBINARY, 16, array(
        ), 'IP to')
    ->addColumn('crawler_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => true
        ), 'Crawler name')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 1
        ), 'Status')
    ->addColumn('cms_page_identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Page URL Key')
    ->addColumn('redirect', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => true,
        'default' => 0
        ), 'Redirect')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Modified at')
    ->setComment('Bot protection. Blacklist');
$installer->getConnection()->createTable($table);

/**
 * Create table 'tm_botprotection/whitelist'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tm_botprotection/whitelist'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
        ), 'Id')
    ->addColumn('ip_from', Varien_Db_Ddl_Table::TYPE_VARBINARY, 16, array(
        ), 'IP from')
    ->addColumn('ip_to', Varien_Db_Ddl_Table::TYPE_VARBINARY, 16, array(
        ), 'IP to')
    ->addColumn('crawler_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => true
        ), 'Crawler name')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 1
        ), 'Status')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Modified at')
    ->setComment('Bot protection. Whitelist');
$installer->getConnection()->createTable($table);

/**
 * Create table 'tm_botprotection/pending'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('tm_botprotection/pending'))
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true
        ), 'Id')
    ->addColumn('ip', Varien_Db_Ddl_Table::TYPE_VARBINARY, 16, array(
        ), 'IP')
    ->addColumn('user_agent', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true
        ), 'User agent')
    ->addColumn('crawler_name', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        'nullable'  => true
        ), 'Crawler name')
    ->addColumn('ask_confirm_human', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 0
        ), 'Ask confirm human')
    ->addColumn('confirmed_human', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable' => false,
        'default' => 0
        ), 'Confirmed human')
    ->addColumn('failed_attempts', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'default' => 0
        ), 'Failed confirmation attempts')
    ->addColumn('update_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Modified at')
    ->setComment('Bot protection. Pending list');
$installer->getConnection()->createTable($table);

$installer->endSetup();
