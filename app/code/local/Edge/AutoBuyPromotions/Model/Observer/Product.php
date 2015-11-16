<?php

class Edge_AutoBuyPromotions_Model_Observer_Product
{
    public function autoBuyPromotions(Varien_Event_Observer $observer)
    {
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $quote = Mage::getSingleton('sales/quote')->load($quoteId);

        $rules = Mage::getResourceModel('salesrule/rule_collection')
            ->setValidationFilter(Mage::app()->getStore()->getWebsiteId(), $quote->getCustomerGroupId())
            ->addAutoBuyPromotionFilter()
            ->load();
        if ($rules->count() < 1) {
            return;
        }
        foreach ($rules as $rule) {
            $rule->afterLoad();
        }

        $fakeQuote = clone $quote;
        $fakeQuote->setId(null);

        $product = Mage::getModel('catalog/product')->load($observer->getProduct()->getId());

        $item = Mage::getModel('sales/quote_item')->setQuote($fakeQuote)->setProduct($product);
        $item->setAllItems(array($product));
        $item->getProduct()->setProductId($product->getEntityId());
        $item->setQty(1);
        
        $item->getQuote()->setData('items_collection', array($item));

        $addProductsToCart = array();
        foreach ($rules as $rule) {
            if ($rule->getIsActive()) {
                if ($rule->getConditions()->validate($item)) {
                    foreach ($rule->getProductId() as $productId) {
                        $cartProduct = Mage::getModel('catalog/product')
                            ->setStoreId(Mage::app()->getStore()->getId())
                            ->load($productId);
                        if ($cartProduct->isAvailable()) {
                            $addProductsToCart[] = $cartProduct;
                        }
                    }
                }
            }
        }

        if (!empty($addProductsToCart)) {
            foreach ($addProductsToCart as $cartProduct) {
                $quote->addProduct($cartProduct);
            }
            $quote->collectTotals()->save();
        }
    }
}
