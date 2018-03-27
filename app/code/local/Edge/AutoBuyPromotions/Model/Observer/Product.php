<?php

class Edge_AutoBuyPromotions_Model_Observer_Product
{
    public function autoBuyPromotions(Varien_Event_Observer $observer)
    {
        $resource = Mage::getSingleton('core/resource');
        $quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        $quote = Mage::getSingleton('sales/quote')->load($quoteId);

        $rules = Mage::getResourceModel('salesrule/rule_collection')
            ->setValidationFilter(Mage::app()->getStore()->getWebsiteId(), $quote->getCustomerGroupId());
        
        $rules->getSelect()->join(
            array($resource->getTableName('autobuypromotions/autobuypromotions')),
            'main_table.rule_id = autobuypromotions.rule_id',
            array()
        )->group('rule_id');
            
        $rules->load();
            
        if ($rules->count() < 1) {
            return;
        }
        
        foreach ($rules as $rule) {
            $rule->afterLoad();
            if (!$rule->getIsActive()) {
                return;
            }
            
            if ($rule->validate($quote->getShippingAddress())) {
                foreach ($rule->getProductId() as $productId) {
                    $addProduct = true;
                    foreach ($quote->getAllVisibleItems() as $item) {
                        if ($item->getProductId() === $productId) {
                            $addProduct = false;
                        }
                    }
                    
                    if ($addProduct) {
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
