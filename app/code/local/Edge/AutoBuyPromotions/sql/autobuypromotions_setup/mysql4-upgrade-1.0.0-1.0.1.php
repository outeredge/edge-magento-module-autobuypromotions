<?php

$this->startSetup()->run("

    CREATE TABLE IF NOT EXISTS {$this->getTable('autobuypromotions/autobuypromotions')} (
        `rule_id` int(10) NOT NULL,
        `product_id` int(10) unsigned NOT NULL,
        PRIMARY KEY (`rule_id`,`product_id`),
        KEY `IDX_AUTOBUYPROMOTIONS_PRODUCT_ID` (`product_id`),
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_RULE_ID` FOREIGN KEY (`rule_id`)
            REFERENCES `salesrule` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_PRODUCT_ID` FOREIGN KEY (`product_id`)
            REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ALTER TABLE {$this->getTable('salesrule/rule')}
        ADD COLUMN `is_auto_buy_promotion` smallint(6) NOT NULL DEFAULT '0';

")->endSetup();