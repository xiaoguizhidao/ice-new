<?php
/**
* @author Amasty Team
* @copyright Amasty
* @package Amasty_Ogrid
*/
class Amasty_Ogrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Images extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $entityId = $row->getData('entity_id');
        
        $collection = Mage::getModel("amogrid/order_item")->getCollection();
        
        $collection->getSelect()->join( 
            array(
                'orderItem' => $collection->getTable('sales/order_item')
            ),
            'main_table.item_id = orderItem.item_id', 
            array("orderItem.product_id")
        );
        
        $collection->addFieldToFilter("orderItem.order_id", $entityId);
        
        $html = array();
        
        $data = $collection->getData();
        
        $images = array();
        foreach($data as $order => $item){
            $product = Mage::getModel("catalog/product")->load($item["product_id"]);

            if ($product->getThumbnail() !== NULL && $product->getThumbnail() != 'no_selection' ){
                $images[] = "<img src='". $product->getThumbnailUrl() ."'/>";
            }
        }
        
        if (count($images) > 0){
            $visibleSlided = 3;
            $widthImg = 75;
            $paddingImg = 2;
            $scrollerWidth = 40;
            
            $showCarousel = count($images) > $visibleSlided;
            $html[] = '
                <div class="carousel" id="carousel_'.$entityId.'" style="'.($showCarousel ? 'width: '.(($widthImg + $paddingImg)*$visibleSlided+$scrollerWidth).'px;' : '').'">
                    '.($showCarousel ? '
                        <a href="javascript:" class="carousel-control prev" rel="prev"><</a>
                        <a href="javascript:" class="carousel-control next"  rel="next">></a>
                    ' : '').'
                    <div class="am_middle" style="width: '.($showCarousel ? $visibleSlided * ($widthImg + $paddingImg) : '').'px;">
                        <div class="am_inner" style="width: '.(count($images) * ($widthImg + $paddingImg)).'px;">
                            ' . implode('', $images) . '
                        </div>
                    </div>
                </div>
                '.($showCarousel ? '
                    <script>
                        new Carousel(
                            $(\'carousel_'.$entityId.'\').down(\'.am_middle\'), 
                            $(\'carousel_'.$entityId.'\').down(\'.am_inner\').select(\'img\'), 
                            $(\'carousel_'.$entityId.'\').select(\'a\'), {
                                    duration: 0.7,
                                    visibleSlides: '.$visibleSlided.'
                        });
                    </script>
                ' : '').'
                
            ';
        }
        
        return implode("<br/>", $html);
        
    }
}