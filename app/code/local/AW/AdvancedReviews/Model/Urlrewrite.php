<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_AdvancedReviews
 * @version    2.3.2
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_AdvancedReviews_Model_Urlrewrite extends Mage_Core_Controller_Varien_Router_Abstract
{
    /*
     * Suffix of the AdvancedReviews page URLs
     */
    const page_suffix = '-reviews/';
    const review_suffix = '-review-';

    public function init($observer)
    {
        $front = $observer->getEvent()->getFront();

        $advancedReviews = new AW_AdvancedReviews_Model_Urlrewrite();
        $front->addRouter('advancedreviews', $advancedReviews);

    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $path = $request->getPathInfo();

        if (strpos($path, self::review_suffix)) {
            preg_match_all('/(-review-)(\d)*/', $path, $matchesarray);
            $reviewId = substr(implode('', reset($matchesarray)), strlen(self::review_suffix));

            if ($reviewId) {
                $request->setModuleName('review')
                    ->setControllerName('product')
                    ->setActionName('view');
                $request->setParam('id', $reviewId);
                return true;
            }
        } elseif (strpos($path, self::page_suffix)) {
            $reqPath = null;
            $toStore = $request->getParam('___store');
            if ($toStore) {
                $storesCollection = Mage::getModel('core/store')->getCollection();
                $storesCollection->addFieldToFilter('code', array('eq' => $toStore));
                $toStoreIdArr = $storesCollection->getData();
                $toStoreId = @$toStoreIdArr[0]['store_id'];
            }

            $path = str_replace(self::page_suffix, '', $path);

            $collection = Mage::getModel('core/url_rewrite')->getCollection();
            $collection->getSelect()
                ->where('request_path = ?', substr($path, 1));
            $dataArr = $collection->getData();
            $productId = @$dataArr[0]['product_id'];
            $categoryId = @$dataArr[0]['category_id'];
            $targetPath = @$dataArr[0]['target_path'];
            if ($productId && $toStore) {
                $collection = Mage::getModel('core/url_rewrite')->getCollection();
                $collection->addFieldToFilter('product_id', array('eq' => $productId))
                    ->addFieldToFilter('category_id', array('eq' => $categoryId))
                    ->addFieldToFilter('target_path', array('eq' => $targetPath))
                    ->addFieldToFilter('store_id', array('eq' => $toStoreId))
                ;
            }

            foreach ($collection->getData() as $rule) {
                $id = $rule['product_id'];
                $category = $rule['category_id'];
            }
            if (isset($id)) {
                $request->setModuleName('review')
                    ->setControllerName('product')
                    ->setActionName('list');
                $request->setParam('id', $id);

                if ($category) {
                    $request->setParam('category', $category);
                }

                return true;
            }
        }
    }
}