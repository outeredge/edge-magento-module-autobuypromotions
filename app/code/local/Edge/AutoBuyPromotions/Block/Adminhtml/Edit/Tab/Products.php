<?php

class Edge_AutoBuyPromotions_Block_Adminhtml_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_product_grid');
        $this->setUseAjax(true);
    }

    public function getRule()
    {
        return Mage::registry('autobuypromotions_rule');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in auto add products flag
        if ($column->getId() == 'selected_auto_buy_promotions_products') {
            $productIds = $this->_getSelectedAutoBuyPromotionsProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            elseif(!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('*');

        if (!$this->getRequest()->getParam('ajax')) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedAutoBuyPromotionsProducts()));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('selected_auto_buy_promotions_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'selected_auto_buy_promotions_products',
            'values'    => $this->_getSelectedAutoBuyPromotionsProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('brand')->__('ID'),
            'width'  => '50px',
            'type'   => 'number',
            'index'  => 'entity_id'
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('brand')->__('Name'),
            'index'  => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('brand')->__('SKU'),
            'index'     => 'sku',
            'width'     => '200px',
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('brand')->__('Price'),
            'index'         => 'price',
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsGrid', array('_current'=>true));
    }

    protected function _getSelectedAutoBuyPromotionsProducts()
    {
        $products = $this->getRequest()->getPost('selected_auto_buy_promotions_products');
        if (is_null($products)) {
            $products = $this->getRule()->getAutoBuyPromotionsProducts();
            return array_keys($products);
        }
        return $products;
    }

    public function getSelectedAutoBuyPromotionsProducts()
    {
        $products = array();
        foreach ($this->getRule()->getAutoBuyPromotionsProducts() as $product) {
            $products[] = $product->getId();
        }
        return $products;
    }
}