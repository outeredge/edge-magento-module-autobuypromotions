<?php

class Edge_AutoBuyPromotions_Model_Observer_Product
{
    public function autoBuyPromotions(Varien_Event_Observer $observer)
    {
        $rules = Mage::getModel('salesrule/rule')->getCollection()
            ->addFieldToFilter('auto_buy_promotions_product_ids', array('neq' => null));

        if (empty($rules)) {
            return;
        }

        $item = $observer->getEvent()->getQuoteItem();

        $quote = Mage::getModel('sales/quote');
        $validateItem = $quote->addProduct($item->getProduct());

        $cart = Mage::getSingleton('checkout/cart');
        foreach ($rules as $rule) {
            if ($rule->getConditions()->validate($validateItem)){
                foreach ($rule->getAutoBuyPromotionsProducts() as $product) {
                    $cartProduct = Mage::getModel('catalog/product')->load($product->getId());
                    $cart->addProduct($cartProduct);
                }
            }
        }

        $cart->save();
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }
}
