<?php

$this->startSetup()->run("

    ALTER TABLE {$this->getTable('salesrule/rule')}
        ADD COLUMN `keywords` text NULL DEFAULT NULL,
        ADD COLUMN `priority` int(11) NOT NULL DEFAULT '0';

    CREATE TABLE IF NOT EXISTS {$this->getTable('autobuypromotions/category')} (
        `rule_id` int(10) NOT NULL,
        `category_id` int(10) unsigned NOT NULL,
        PRIMARY KEY (`rule_id`,`category_id`),
        KEY `IDX_AUTOBUYPROMOTIONS_CATEGORY_CATEGORY_ID` (`category_id`),
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_CATEGORY_RULE_ID` FOREIGN KEY (`rule_id`)
            REFERENCES `salesrule` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_CATEGORY_CATEGORY_ID` FOREIGN KEY (`category_id`)
            REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE IF NOT EXISTS {$this->getTable('autobuypromotions/product')} (
        `rule_id` int(10) NOT NULL,
        `product_id` int(10) unsigned NOT NULL,
        PRIMARY KEY (`rule_id`,`product_id`),
        KEY `IDX_AUTOBUYPROMOTIONS_PRODUCT_PRODUCT_ID` (`product_id`),
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_PRODUCT_RULE_ID` FOREIGN KEY (`rule_id`)
            REFERENCES `salesrule` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_AUTOBUYPROMOTIONS_PRODUCT_PRODUCT_ID` FOREIGN KEY (`product_id`)
            REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();
