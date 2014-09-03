<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Ice.com (http://www.ice.com)
 */
class Oro_Ice_Block_Advancedreviews_Summary extends AW_AdvancedReviews_Block_Summary
{

	/*
	 *	@return associative array
	 * Fills in the missing review values with empty qty and percent values
	 * This let's us see the full list of star reiews
	 */
	public function getSummaryBlock()
	{
		$ratings = parent::getSummaryBlock();
		$sortedRatings = array();
		foreach($ratings as $i => $rating){
			if(isset($rating['votes'])){
				for ($j = 5; $j >= 1; $j--) {
					if (!isset($ratings[$i]['votes'][$j])) {
						$ratings[$i]['votes'][$j] = array('qty' => 0, 'percent' => 0);
					}
				}
				// sort ratings from highest to lowest
				krsort($ratings[$i]['votes']);
			}
		}

		return $ratings;
	}

	/*
	 * 
	 * @param array $ratings 
	 * @return - returns the average start number. example: 4.7 out 5
	 */
	public function getAverageStarRating(array $ratings)
	{
		$total = 0;
		$totalQty = 0;
		$niceNum = 0;
		$stars = array_keys($ratings);
		rsort($stars);
		$highestStar = $stars[0];
		foreach($ratings as $star => $rating)
		{
			$total += ($star * $rating['qty']);
			$totalQty += $rating['qty'];
		}

		// check for zero so we don't divide by zero
		if($totalQty > 0 && $highestStar){
			$percentage = $total/($totalQty * $highestStar);
			$niceNum = $percentage * $highestStar;
		}
		return round($niceNum,1);

	}
}