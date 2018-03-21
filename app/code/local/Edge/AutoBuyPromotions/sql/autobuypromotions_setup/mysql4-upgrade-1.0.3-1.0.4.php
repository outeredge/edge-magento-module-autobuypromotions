<?php

$this->startSetup()->run("

    ALTER TABLE `{$this->getTable('sales/quote_item')}`
        ADD COLUMN `auto_buy_promotion_link` int(10) unsigned NULL DEFAULT NULL,
        ADD KEY `IDX_QUOTE_ITEM_AUTOBUYPROMOTIONS_LINK` (`auto_buy_promotion_link`),
        ADD CONSTRAINT `FK_QUOTE_ITEM_AUTOBUYPROMOTIONS_LINK` FOREIGN KEY(`auto_buy_promotion_link`)
            REFERENCES `{$this->getTable('sales/quote_item')}` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

")->endSetup();

$this->startSetup()->run("

    CREATE TABLE IF NOT EXISTS {$this->getTable('autobuypromotions/brand')} (
        `rule_id` int(10) NOT NULL,
        `brand_id` int(11) unsigned NOT NULL,
        PRIMARY KEY (`rule_id`,`brand_id`),
        KEY `IDX_AUTOBUYPROMOTIONS_BRAND_BRAND_ID` (`brand_id`),
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_BRAND_RULE_ID` FOREIGN KEY (`rule_id`)
            REFERENCES `salesrule` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_BRAND_BRAND_ID` FOREIGN KEY (`brand_id`)
            REFERENCES `brand` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();


$this->startSetup()->run("

    ALTER TABLE {$this->getTable('salesrule/rule')}
        ADD COLUMN `order_total` DECIMAL(12,4) NULL DEFAULT NULL;

")->endSetup();

$this->startSetup()->run("

    ALTER TABLE `{$this->getTable('sales/quote_item')}`
        ADD COLUMN `auto_buy_promotion_rule` int(10) unsigned NULL DEFAULT NULL,
        ADD KEY `IDX_QUOTE_ITEM_AUTOBUYPROMOTIONS_RULE` (`auto_buy_promotion_rule`),
        ADD CONSTRAINT `FK_QUOTE_ITEM_AUTOBUYPROMOTIONS_RULE` FOREIGN KEY(`auto_buy_promotion_rule`)
            REFERENCES `{$this->getTable('salesrule/rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE;

")->endSetup();