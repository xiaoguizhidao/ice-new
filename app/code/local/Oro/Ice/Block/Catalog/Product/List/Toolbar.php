<?php

/**
* Class adds custom collection sorting by rating and most reviewed
*/

class Oro_Ice_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{

    /**
     * Set collection to pager. Adds conditions for sorting by rating and review
     *
     * @param Varien_Data_Collection $collection
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        
        if($this->getCurrentOrder() == 'rating') {

            $this->_collection->sortByRating($this->getCurrentDirection());
        }else if ($this->getCurrentOrder() == 'review'){
            $this->_collection->sortByMostReviewed($this->getCurrentDirection());
        }else if ($this->getCurrentOrder()) {

            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;

    }


    /**
     * Adds rating and reviews as sort options and removes name and position
     *
     *
     * @return array of available sorting options
     */
    public function getAvailableOrders()
    {
        if(isset($this->_availableOrder['name'])){
          unset($this->_availableOrder['name']);
        }
        if(isset($this->_availableOrder['position'])){
          unset($this->_availableOrder['position']);
        }

        return $this->_availableOrder;
    }

}
