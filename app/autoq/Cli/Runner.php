<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Queue\QueueControl;
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

                        $this->logForQueueItem($queueItem, "Query returns a resultset, fetching data...");


                    } else {
                        $this->logForQueueItem($queueItem, "No resultset received for this query, marking queue item complete");
                        //Mark queue item complete
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
    
    private function saveResultSetToStaging($queueItem, $results) {

        

        if ($handle = fopen($filename, 'w')) {

            $rowsWritten = 0;
            $writeError = false;
            $firstRow = true;

            while (($row = $connection->fetch($result)) !== false) {	//Rely on lazy eval

                if ($firstRow) {
                    fputcsv($handle, array_keys($row));
                    $firstRow = false;
                }

                if (fputcsv($handle, $row) !== false) {
                    $rowsWritten++;
                } else {
                    $this->errorMessage = 'Problem saving the csv file: ' . $filename;
                    $this->log->_l($this->errorMessage, BasicLogger::ERROR);
                    $writeError = true;
                    break;
                }
            }
            fclose($handle);

            if (!$writeError) {
                $this->log->_l('File ' . $filename . ' saved ok. ' . $rowsWritten . ' rows written.');

                $this->log->_l('Stripping bad ASCII control characters from ' . $filename);

                $chr = chr(26);

                $cmd = "sed -i 's/$chr//g' " . escapeshellarg($filename);
                $this->log->_l('CMD: ' . $cmd);
                system($cmd);

                $success = true;
            }

        } else {
            $this->errorMessage = 'Unable to create file ' . $filename . ' for staging result set';
            $this->log->_l($this->errorMessage);
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
        $message = "Queue item: {$queueItem->getId()} for Job Id: {$queueItem->getJobDefintion()->getId()} ";

        $this->log->log($type, $message);
    }

}