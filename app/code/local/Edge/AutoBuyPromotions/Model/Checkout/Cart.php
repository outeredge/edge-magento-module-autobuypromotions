<?php

class Edge_AutoBuyPromotions_Model_Checkout_Cart extends Edge_UniqueQuoteItem_Model_Checkout_Cart
{
    public function updateItems($data)
    {
        Mage::dispatchEvent('checkout_cart_update_items_before', array('cart'=>$this, 'info'=>$data));

        foreach ($data as $itemId => $itemInfo) {
            $item = $this->getQuote()->getItemById($itemId);
            if (!$item) {
                continue;
            }

            $similarItems = Mage::helper('uniquequoteitem')->getSimilarItems($item);

            if (!empty($itemInfo['remove']) || (isset($itemInfo['qty']) && $itemInfo['qty']=='0')) {
                foreach ($similarItems as $similarItem) {
                    $this->removeItem($similarItem->getItemId());
                }
                continue;
            }

            $userQty = isset($itemInfo['qty']) ? (float) $itemInfo['qty'] : false;
            $quoteQty = sizeof($similarItems);
            $qtyChange = $userQty - $quoteQty;

            if ($qtyChange > 0) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($item->getProductId());

                $autoBuyItems = Mage::helper('autobuypromotions')->getRelatedAutoBuyLinkItems($item);

                for ($i=0; $i<$qtyChange; $i++) {
                    $this->addProduct($product, $item->getBuyRequest());

                    // Ensures quote can be saved now and after this code
                    $this->getQuote()->setData('updated', true);
                    $this->getQuote()->save();
                    $this->getQuote()->setData('updated', true);

                    if ($autoBuyItems->count()) {
                        $latestAddedItemId = Mage::getModel('sales/quote_item')
                            ->getCollection()
                            ->setQuote($this->getQuote())
                            ->addFieldToFilter('parent_item_id', array('null' => true))
                            ->setOrder('item_id', 'DESC')
                            ->getFirstItem()
                            ->getId();
                        foreach ($autoBuyItems as $autoBuyItem) {

                            $rule = Mage::getModel('salesrule/rule')->load($autoBuyItem->getAutoBuyPromotionRule());
                            if ($rule->getDiscountQty() > 0) {
                                $autoBuyLinkItems = Mage::helper('autobuypromotions')->getQuoteAutoBuyLinkItems($this->getQuote(), $autoBuyItem->getProductId());
                                if ($autoBuyLinkItems->count() >= $rule->getDiscountQty()) {
                                    continue;
                                }
                            }

                            $autoBuyProduct = Mage::getModel('catalog/product')
                                ->setStoreId(Mage::app()->getStore()->getId())
                                ->load($autoBuyItem->getProductId());
                            $autoBuyProduct->setAutoBuyPromotionLink($latestAddedItemId);
                            $this->addProduct($autoBuyProduct, $autoBuyItem->getBuyRequest());
                        }
                    }
                }
            }
            elseif ($qtyChange < 0) {
                $x=0;
                for ($i=0; $i>$qtyChange; $i--) {
                    $this->removeItem($similarItems[$x]->getItemId());
                    $x++;
                }
            }
        }

        Mage::dispatchEvent('checkout_cart_update_items_after', array('cart'=>$this, 'info'=>$data));
        return $this;
    }
}