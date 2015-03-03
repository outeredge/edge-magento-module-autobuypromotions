<?php

$this->startSetup();

// SalesRule Auto Buy Promotions Products
$this->run("
    ALTER TABLE {$this->getTable('salesrule/rule')}
        ADD COLUMN `auto_buy_promotions_product_ids` TEXT NULL DEFAULT NULL;
");

$this->endSetup();