<?php

$installer = $this;

$installer->startSetup();

/**
 * Create additional index for table 'log/url_table'
 */

$installer->getConnection()->addIndex(
    $installer->getTable('log/url_table'),
    'BOT_PROTECTION_VISIT_TIME',
    array('visit_time'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX
);

$installer->endSetup();
