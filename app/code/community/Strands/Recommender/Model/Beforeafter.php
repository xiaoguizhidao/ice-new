<?php


/**
 * Used in creating options for Numbers selection
 *
 */
class Strands_Recommender_Model_Beforeafter
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>'before'),
            array('value' => 1, 'label'=>'after'),
        );
    }

}

?>