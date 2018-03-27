<?php

class Edge_AutoBuyPromotions_Model_Observer_QuoteItem
{
    public function setAutoBuyPromotionLink(Varien_Event_Observer $observer)
    {
        $item = $observer->getQuoteItem();
        if ($item->getProduct()->getAutoBuyPromotionLink()) {
            $item->setAutoBuyPromotionLink($item->getProduct()->getAutoBuyPromotionLink());
        }
    }

    public function removeAutoBuyPromotionLinkItem(Varien_Event_Observer $observer)
    {
        $item = $observer->getQuoteItem();

        $autoBuyLinkItems = Mage::helper('autobuypromotions')->getRelatedAutoBuyLinkItems($item);
        if ($autoBuyLinkItems->count()) {
            foreach ($autoBuyLinkItems as $autoBuyLinkItem) {
                $item->getQuote()->removeItem($autoBuyLinkItem->getId());
            }
        }
    }

    public function setAutoBuyPromotionRule(Varien_Event_Observer $observer)
    {
        $item = $observer->getQuoteItem();
        if ($item->getProduct()->getAutoBuyPromotionRule()) {
            $item->setAutoBuyPromotionRule($item->getProduct()->getAutoBuyPromotionRule());
        }
    }

    public function removeAutoBuyPromotionRuleItem(Varien_Event_Observer $observer)
    {
        $cart = $observer->getCart();

        $autoBuyRuleItems = Mage::helper('autobuypromotions')->getQuoteAutoBuyRuleItems($cart->getQuote());
        if ($autoBuyRuleItems->count()) {
            foreach ($autoBuyRuleItems as $autoBuyRuleItem) {
                $rule = Mage::getModel('salesrule/rule')->load($autoBuyRuleItem->getAutoBuyPromotionRule());
                if (!$rule->validate($cart->getQuote()->getShippingAddress())) {
                    $cart->removeItem($autoBuyRuleItem->getId());
                    foreach ($cart->getQuote()->getAddressesCollection() as $address) {
                        $address->unsetData('cached_items_all');
                        $address->unsetData('cached_items_nominal');
                        $address->unsetData('cached_items_nonnominal');
                    }
                }
            }

            $cart->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
        }
    }
}
