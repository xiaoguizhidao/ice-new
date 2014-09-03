<?php
/**
 * @category   Oro
 * @package    Oro_Dataflow
 * @copyright  Copyright (c) 2014 Oro Inc. DBA MageCore (http://www.magecore.com)
 */
interface Oro_Dataflow_Model_Schedule_Interface
{
    const STATUS_PENDING    = 'pending';
    const STATUS_CANCELED   = 'canceled';
    const STATUS_SUCCESS    = 'success';
    const STATUS_FAILED     = 'failed';
    const STATUS_IN_PROCESS = 'in_process';
    const STATUS_SUSPEND    = 'suspend';

    /**
     * Execute schedule
     *
     * @return $this
     */
    public function execute();

    /**
     * @return array
     */
    public function getLog();
}
