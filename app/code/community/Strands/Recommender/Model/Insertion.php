<?php


/**
 * Used to select the widgets' insertion method
 *
 */
class Strands_Recommender_Model_Insertion
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(            
            array('value' => 0, 'label'=>'manual - insertion via file'),
            array('value' => 1, 'label'=>'manual - insertion via CMS pages'),
       		array('value' => 2, 'label'=>'quick & easy - automatic insertion'),
        );
    }

}

?>