<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Helper Class
 */
class Oro_Ice_Helper_Product_Collection_Sort extends Mage_Core_Helper_Abstract
{
        /**
        * Sets the product collection query to grab ratings data and sort by
        * rating summary
        *
        * @param string $dir current direction for sort order
        */
    public function sortByRating($collection, $dir)
    {   
        $entity_code_id = Mage::getModel('review/review')
            ->getEntityIdByCode(Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE);

        $collection->joinField('rating_summary','review_entity_summary','rating_summary','entity_pk_value=entity_id',
            array(
                    'entity_type'=>$entity_code_id,
                    'store_id'=> Mage::app()->getStore()->getId()
            ), 'left');

            $collection->setOrder("rating_summary", $dir);

        return $collection;
    }

    /**
   * Sets the product collection query to grab a review count sort by
     * the count of reviews
     *
     * @param string $dir current direction for sort order
     */
    public function sortByMostReviewed($collection, $dir)
    {
        $entity_code_id = Mage::getModel('review/review')
            ->getEntityIdByCode(Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE);
        
        $collection->joinField('reviews_count', 'review_entity_summary', 'reviews_count', 'entity_pk_value=entity_id',
            array('entity_type' => $entity_code_id,
                'store_id' => Mage::app()->getStore()->getId()),
            'left'
        );
        $collection->setOrder('reviews_count', $dir);

        return $collection;

  }

}
