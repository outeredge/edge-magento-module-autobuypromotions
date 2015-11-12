<?php

class Edge_AutoBuyPromotions_Model_Resource_Rule_Collection extends Mage_SalesRule_Model_Resource_Rule_Collection
{
    public function addAutoBuyPromotionFilter()
    {
        $this->addFieldToFilter('is_auto_buy_promotion', array('eq' => true));

        $this->getSelect()
            ->join(
                array($this->getTable('autobuypromotions/autobuypromotions')),
                "main_table.rule_id = autobuypromotions.rule_id",
                array()
            )
            ->group('rule_id');

        return $this;
    }
}
