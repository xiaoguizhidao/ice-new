<?php
/**
 * @category   Oro
 * @package    Oro_Ice
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */

/**
 * Class to render custom 3 column 2nd level menu
 */
class Oro_Ice_Block_Catalog_Topmenu extends Mage_Page_Block_Html_Topmenu
{

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param Varien_Data_Tree_Node $menuTree
     * @param string $childrenWrapClass
     * @return string
     */
    protected function _getHtml(Varien_Data_Tree_Node $menuTree, $childrenWrapClass)
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = is_null($parentLevel) ? 0 : $parentLevel + 1;

        $counter = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        $columns = array();
        foreach ($children as $child) {

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $child->setClass($outermostClass);
            }

            $childHtml = '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $childHtml .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '><span>'
                . $this->escapeHtml($child->getName()) . '</span></a>';

            if ($child->hasChildren()) {
                if (!empty($childrenWrapClass)) {
                    $childHtml .= '<div class="' . $childrenWrapClass . '">';
                }

                $childHtml .= '<ul class="level' . $childLevel . '">';
                $childHtml .= $this->_getHtml($child, $childrenWrapClass);
                if ($childLevel == 0) {
                    $linkText = $child->getAdditional()->getData(Oro_Ice_Helper_Data::ATTRIBUTE_VIEWMORE_LINK);
                    $childHtml .= '<li class="iceus_menu_viewmore_link"><a href="'.$child->getUrl().'">';
                    $childHtml .= $linkText ? $linkText : $this->__('View all %s', $child->getName());
                    $childHtml .= '</a></li>';
                }
                $childHtml .= '</ul>';

                if (!empty($childrenWrapClass)) {
                    $childHtml .= '</div>';
                }
            }
            $childHtml .= '</li>';

            if ($childLevel == 1) { // hardcoded for now since design will be changed
                $columnNumber = $child->getAdditional()->getData(Oro_Ice_Helper_Data::ATTRIBUTE_COLUMN_NUMBER);
                if ($columnNumber) {
                    if (!isset($columns[$columnNumber])) {
                        $columns[$columnNumber] = array(
                            'label' => $menuTree->getAdditional()
                                    ->getData(Oro_Ice_Helper_Data::ATTRIBUTE_COLUMN_LABEL.$columnNumber),
                            'html' => ''
                        );
                    }
                    $columns[$columnNumber]['html'] .= $childHtml;
                }
            } else {
                $html .= $childHtml;
            }

            $counter++;
        }

        foreach ($columns as $col) {
            $html .= '<li class="column"><ul>';
            $html .= '<li class="iceau_menu_label">' . $col['label'] . '</li>';
            $html .= $col['html'];
            $html .= '</ul></li>';
        }

        return $html;
    }

} 
