<?php

namespace Autoq\Cli;

use Autoq\Data\Queue\QueueControl;
use Autoq\Data\Queue\FlowControl;
use Autoq\Data\Queue\QueueItem;
use Autoq\Services\DbConnectionMgr;
use Autoq\Services\DbCredentialsService;
use Phalcon\Config;
use Phalcon\Db\Adapter;
use Phalcon\Db\ResultInterface;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Runner implements CliTask
{

    protected $config;
    protected $log;
    protected $queueControl;
    private $dbConnectionMgr;
    private $dbCredentialsService;

    /**
     * Runner constructor.
     * @param Config $config
     * @param Stream $log
     * @param QueueControl $queueControl
     * @param DbConnectionMgr $dbConnectionMgr
     * @param DbCredentialsService $dbCredentialsService
     */
    public function __construct(Config $config, Stream $log, QueueControl $queueControl, DbConnectionMgr $dbConnectionMgr, DbCredentialsService $dbCredentialsService)
    {
        $this->config = $config;
        $this->log = $log;
        $this->queueControl = $queueControl;
        $this->dbConnectionMgr = $dbConnectionMgr;
        $this->dbCredentialsService = $dbCredentialsService;
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

                    //@todo - bail if error, pass creds through to getConnection
                    $dbCredSet = $this->dbCredentialsService->getByAlias($queueItem->getJobDefintion()->getConnection());

                    /**
                     * @var $connection Adapter\Pdo
                     */
                    if (($connection = $this->dbConnectionMgr->getConnection($dbCredSet)) !== null) {

                        $this->logForQueueItem($queueItem, "Connection to database established, now executing job query..");

                        $results = $connection->query($queueItem->getJobDefintion()->getQuery());

                        if ($this->hasResultSet($results)) {

                            $this->logForQueueItem($queueItem, "Query returns a resultset, saving data...");

                            $this->saveResultSetToStaging($queueItem, $results);

                        } else {
                            $this->logForQueueItem($queueItem, "No resultset received for this query, fetching complete");
                        }

                        $this->queueControl->updateStatus($queueItem, FlowControl::STATUS_FETCHING_COMPLETE);
                        $connection = null; //Clear db connection
                    } else {
                        throw new \Exception("No database connection established");
                    }

                } catch (\Exception $e) {
                    //If an exception happens whlst running mark change queue status to ERROR, and log 
                    $errMsg = $e->getMessage();
                    $queueItem->getFlowControl()->setErrorMessage($errMsg);
                    $this->queueControl->updateStatus($queueItem, FlowControl::STATUS_ERROR);
                    $this->logForQueueItem($queueItem, $errMsg, Logger::ERROR);
                }

                sleep($this->config['app']['runner_sleep']);
            }
        }
    }

    /**
     * @param QueueItem $queueItem
     * @param ResultInterface $results
     * @throws \Exception
     */
    private function saveResultSetToStaging(QueueItem $queueItem, ResultInterface $results)
    {
        $dir = rtrim($this->config['app']['runner_staging_dir'], '/') . '/';

        $filepath = $dir . $queueItem->getDataStageKey() . '.csv';

        if ($handle = fopen($filepath, 'w')) {

            $rowsWritten = 0;
            $firstRow = true;

            $results->setFetchMode(\PDO::FETCH_ASSOC);

            while (($row = $results->fetch()) !== false) {

                //Write header row
                if ($firstRow) {
                    fputcsv($handle, array_keys($row));
                    $firstRow = false;
                }

                if (fputcsv($handle, $row) !== false) {
                    $rowsWritten++;
                } else {
                    fclose($handle);
                    throw new \Exception("There was a problem saving the staging file: $filepath");
                }
            }

            fclose($handle);

            $this->log->info("$rowsWritten rows successfully written to $filepath");

        } else {
            throw new \Exception("Unable to create file $filepath for staging result set");
        }
    }

    /**
     * @param ResultInterface $results
     * @return bool
     */
    private function hasResultSet(ResultInterface $results)
    {
        $pdoResult = $results->getInternalResult();

        return $pdoResult->columnCount() > 0;
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