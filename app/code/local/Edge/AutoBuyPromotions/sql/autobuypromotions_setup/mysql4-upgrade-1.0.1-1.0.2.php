<?php

$this->startSetup()->run("

    ALTER TABLE {$this->getTable('salesrule/rule')}
        ADD COLUMN `trigger_product` int(10) NULL DEFAULT NULL;

")->endSetup();