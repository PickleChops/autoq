<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Queue\QueueControl;
use Autoq\Data\Queue\QueueFlow;
use Autoq\Data\Queue\QueueItem;
use Autoq\Services\DbConnectionMgr;
use Phalcon\Config;
use Phalcon\Db\Adapter;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Runner implements CliTask
{

    protected $config;
    protected $log;
    protected $queueControl;
    private $dbConnectionMgr;

    /**
     * Runner constructor.
     * @param Config $config
     * @param Stream $log
     * @param QueueControl $queueControl
     * @param DbConnectionMgr $dbConnectionMgr
     */
    public function __construct(Config $config, Stream $log, QueueControl $queueControl, DbConnectionMgr $dbConnectionMgr)
    {
        $this->config = $config;
        $this->log = $log;
        $this->queueControl = $queueControl;
        $this->dbConnectionMgr = $dbConnectionMgr;
    }

    /**
     * Off we go
     * @param array $args
     */
    public function main(Array $args = [])
    {
        $this->log->info("Runner started");

        while (true) {

            /**
             * @var $queueItem QueueItem
             */
            if (($queueItem = $this->queueControl->grabNextNewToFetch()) !== false) {

                try {

                    $this->logForQueueItem($queueItem, "starting data fetch...");

                    /**
                     * @var $connection Adapter\Pdo
                     */

                    $connection = $this->dbConnectionMgr->getConnection('postgres');

                    $this->logForQueueItem($queueItem, "Connection to database established, now executing job query..");

                    /**
                     * @var $results \PDOStatement
                     */
                    $results = $connection->query($queueItem->getJobDefintion()->getQuery());

                    if ($this->hasResultSet($results)) {

                        $this->logForQueueItem($queueItem, "Query returns a resultset, saving data...");

                        $this->saveResultSetToStaging($queueItem, $results);

                        $this->queueControl->endStatus(QueueFlow::STATUS_FETCHING);
                        
                    
                    } else {
                        $this->logForQueueItem($queueItem, "No resultset received for this query, fetching complete");
                        $this->queueControl->endStatus(QueueFlow::STATUS_FETCHING);
                    }

                    $connection = null;

                } catch (\Exception $e) {
                    //log error
                    //Set job as in error state
                    //Record the error in queue
                }


            }
        }
    }

    /**
     * @param QueueItem $queueItem
     * @param \PDOStatement $results
     * @throws \Exception
     */
    private function saveResultSetToStaging(QueueItem $queueItem, \PDOStatement $results)
    {
        $filename = $queueItem->getDataStageKey() . 'csv';

        if ($handle = fopen($filename, 'w')) {

            $rowsWritten = 0;
            $firstRow = true;

            while (($row = $results->fetch(\PDO::FETCH_ASSOC)) !== false) {

                //Write header row
                if ($firstRow) {
                    fputcsv($handle, array_keys($row));
                    $firstRow = false;
                }

                if (fputcsv($handle, $row) !== false) {
                    $rowsWritten++;
                } else {
                    fclose($handle);
                    throw new \Exception("There was a problem saving the staging file: $filename");
                }
            }

            fclose($handle);

            $this->log->info("$rowsWritten rows successfully written to $filename");

        } else {
            throw new \Exception("Unable to create file $filename for staging result set");
        }
    }

    /**
     * @param \PDOStatement $results
     * @return bool
     */
    private function hasResultSet(\PDOStatement $results)
    {
        return $results->columnCount() > 0;
    }

    /**
     * @param QueueItem $queueItem
     * @param $message
     * @param int $type
     */
    private function logForQueueItem(QueueItem $queueItem, $message, $type = Logger::INFO)
    {
        $message = "Queue item: {$queueItem->getId()} for Job Id: {$queueItem->getJobDefintion()->getId()} - $message";

        $this->log->log($type, $message);
    }

}