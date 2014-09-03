<?php
/**
* @category   Oro
* @package    Oro_Ice
* @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
*/

/**
* Class overrides the default product collection class and adds
* custom collection sorting by rating and most reviewed
*/
class Oro_Ice_Model_CatalogSearch_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{

		/**
		 * Calls the sortyByRating method on the product collection sort helper
		 *
		 * @param string $dir current direction for sort order
		 */
	public function sortByRating($dir)
	{
			return Mage::helper('ice/product_collection_sort/')->sortByRating($this, $dir);
	}

	/**
   * Calls the sortyByMostReviewed method on the product collection sort helper
	 *
	 * @param string $dir current direction for sort order
	 */
	public function sortByMostReviewed($dir)
	{
			return Mage::helper('ice/product_collection_sort/')->sortByMostReviewed($this, $dir);
  }

}
