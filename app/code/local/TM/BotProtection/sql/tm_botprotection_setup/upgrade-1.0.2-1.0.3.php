<?php

$installer = $this;

$installer->startSetup();

/**
 * Alter tables with suspiciouse list and blacklist
 */

$tables = array('tm_botprotection/blacklist', 'tm_botprotection/pending');

foreach ($tables as $table) {
    $installer->getConnection()
        ->addColumn(
            $installer->getTable($table),
            'detected_by',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'comment' => 'Detected by'
            )
        );
}

array_push($tables, 'tm_botprotection/whitelist');

foreach ($tables as $table) {
    $installer->getConnection()
        ->addColumn(
            $installer->getTable($table),
            'create_time',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment' => 'Created at'
            )
        );
}

$installer->endSetup();
