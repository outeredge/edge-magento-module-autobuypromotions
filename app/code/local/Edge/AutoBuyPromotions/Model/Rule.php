<?php

class Edge_AutoBuyPromotions_Model_Rule extends Mage_SalesRule_Model_Rule
{
    public function getAutoBuyPromotionsProducts()
    {
        if (!$this->hasAutoBuyPromotionsProducts()) {
            $products = array();

            $autoBuyPromotionsProductIds = $this->getAutoBuyPromotionsProductIds();
            if ($autoBuyPromotionsProductIds) {
                $collection = Mage::getModel('catalog/product')->getCollection()
                    ->addFieldToFilter('entity_id', array('in' => explode('&', $autoBuyPromotionsProductIds)));
                foreach ($collection as $product) {
                    $products[$product->getId()] = $product;
                }
            }

            $this->setAutoBuyPromotionsProducts($products);
        }
        return $this->getData('auto_buy_promotions_products');
    }
}