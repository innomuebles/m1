<?php 
$installer = $this;
$installer->startSetup();
$installer->run("
 ALTER TABLE {$this->getTable('storepickup/storepickup')} ADD `stores_id` varchar(50) NOT NULL;


");
$installer->endSetup(); 
?>