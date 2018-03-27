<?php

class Edge_AutoBuyPromotions_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getControllerModule()
    {
        return 'Edge_AutoBuyPromotions_Adminhtml';
    }
    
    public function getRelatedAutoBuyLinkItems($item)
    {
        return Mage::getModel('sales/quote_item')
            ->getCollection()
            ->setQuote($item->getQuote())
            ->addFieldToFilter('auto_buy_promotion_link', array('eq' => $item->getId()));
    }

    public function getQuoteAutoBuyLinkItems($quote, $productId=null)
    {
        $items = Mage::getModel('sales/quote_item')
            ->getCollection()
            ->setQuote($quote)
            ->addFieldToFilter('auto_buy_promotion_link', array('notnull' => true));

        if ($productId) {
            $items->addFieldToFilter('product_id', array('eq' => $productId));
        }

        return $items;
    }

    public function getQuoteAutoBuyRuleItems($quote, $productId=null)
    {
        $items = Mage::getModel('sales/quote_item')
            ->getCollection()
            ->setQuote($quote)
            ->addFieldToFilter('auto_buy_promotion_link', array('null' => true))
            ->addFieldToFilter('auto_buy_promotion_rule', array('notnull' => true));

        if ($productId) {
            $items->addFieldToFilter('product_id', array('eq' => $productId));
        }

        return $items;
    }
}