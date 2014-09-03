<?php


/**
 * Used in creating options for Cron/Feed selection
 *
 */
class Strands_Recommender_Model_Cronfeed
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>'automatic catalog feed'),
            array('value' => 1, 'label'=>'scheduled upload using Cron'),
            array('value' => 2, 'label'=>'manual catalog upload'),
        );
    }

}

?>