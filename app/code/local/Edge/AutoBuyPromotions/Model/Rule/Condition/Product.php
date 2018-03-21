<?php

class Edge_AutoBuyPromotions_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = Mage::helper('salesrule')->__('Quantity in cart');
        $attributes['quote_item_price'] = Mage::helper('salesrule')->__('Price in cart');
        $attributes['quote_item_row_total'] = Mage::helper('salesrule')->__('Row total in cart');
        $attributes['quote_item_auto_buy_promotion_link'] = Mage::helper('salesrule')->__('Auto Buy Promotion Link');
        $attributes['quote_item_auto_buy_promotion_rule'] = Mage::helper('salesrule')->__('Auto Buy Promotion Rule');
        $attributes['quote_item_is_discounted'] = Mage::helper('salesrule')->__('Is Discounted');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $object->getProduct();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($object->getProductId());
        }

        $product
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice()) // possible bug: need to use $object->getBasePrice()
            ->setQuoteItemRowTotal($object->getBaseRowTotal())
            ->setQuoteItemAutoBuyPromotionLink($object->getAutoBuyPromotionLink())
            ->setQuoteItemAutoBuyPromotionRule($object->getAutoBuyPromotionRule())
            ->setQuoteItemIsDiscounted($this->_itemIsDiscounted($object, $product));

        return parent::validate($product);
    }

    protected function _itemIsDiscounted($object, $product)
    {
        $productId = $object->getProductId();
        if ($object->getParentItemId()) {
            $productId = $object->getParentItem()->getProductId();
        }

        $catalogPriceRuleDiscount = Mage::getModel('catalogrule/rule_product_price')
            ->getCollection()
            ->addFieldToFilter('product_id', array('eq' => $productId))
            ->setPageSize(1)
            ->setCurPage(1)
            ->getSize();

        $specialPrice = $product->getSpecialPrice();
        if ($object->getParentItemId()) {
            $parentProduct = Mage::getModel('catalog/product')->load($object->getParentItem()->getProductId());
            $specialPrice = $parentProduct->getSpecialPrice();
        }

        if ($catalogPriceRuleDiscount > 0 || $specialPrice) {
            return 1;
        }
        return 0;
    }
}
