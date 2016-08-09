<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\OutputEmail;
use Autoq\Data\Jobs\OutputS3;
use Autoq\Data\Queue\QueueControl;
use Autoq\Data\Queue\FlowControl;
use Autoq\Data\Queue\QueueItem;
use Autoq\Services\DbConnectionMgr;
use Autoq\Services\S3CredentialsService;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Phalcon\Config;
use Phalcon\Db\Adapter;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Mvc\View\Simple;

class Sender implements CliTask
{

    protected $config;
    protected $log;
    protected $queueControl;
    private $dbConnectionMgr;
    private $view;

    private $resultsPathInfo;


    const MAX_HTML_ROWS = 50;
    /**
     * @var S3CredentialsService
     */
    private $s3CredentialsService;


    /**
     * Sender constructor.
     * @param Config $config
     * @param Stream $log
     * @param QueueControl $queueControl
     * @param DbConnectionMgr $dbConnectionMgr
     * @param Simple $view
     * @param S3CredentialsService $s3CredentialsService
     */
    public function __construct(Config $config, Stream $log, QueueControl $queueControl, DbConnectionMgr $dbConnectionMgr, Simple $view, S3CredentialsService $s3CredentialsService)
    {
        $this->config = $config;
        $this->log = $log;
        $this->queueControl = $queueControl;
        $this->dbConnectionMgr = $dbConnectionMgr;
        $this->view = $view;
        $this->s3CredentialsService = $s3CredentialsService;
    }

    /**
     * Off we go
     * @param array $args
     */
    public function main(Array $args = [])
    {
        $this->log->info("Sender started");

        while (true) {

            /**
             * @var $queueItem QueueItem
             */
            if (($queueItem = $this->queueControl->grabNextToSend()) !== false) {

                try {

                    $jobDefinition = $queueItem->getJobDefintion();

                    if ($jobDefinition->countOutputs() == 0) {
                        throw new \Exception("No outputs found for job");
                    }

                    $this->logForQueueItem($queueItem, "{$jobDefinition->countOutputs()} outputs defined in job");

                    $this->resultsPathInfo = $this->getResultSetStagePathInfo($queueItem->getDataStageKey());

                    $this->logForQueueItem($queueItem, "Using results from {$this->resultsPathInfo['dirname']}/{$this->resultsPathInfo['basename']}");


                    foreach ($jobDefinition->getOutputs() as $output) {

                        switch ($output->getType()) {
                            case JobDefinition::OUTPUT_EMAIL:

                                /**
                                 * @var $outputEmail OutputEmail
                                 */
                                $outputEmail = $output;

                                $this->outputEmail($queueItem, $outputEmail);

                                break;
                            case JobDefinition::OUTPUT_S3:

                                /**
                                 * @var $outputS3 OutputS3
                                 */
                                $outputS3 = $output;

                                $this->outputS3($queueItem, $outputS3);

                                break;
                            default:
                                $this->logForQueueItem($queueItem, "Unknown output type {$output->getType()}", Logger::ERROR);
                                break;

                        }
                    }

                    $this->queueControl->updateStatus($queueItem, FlowControl::STATUS_COMPLETED);

                } catch (\Exception $e) {
                    //If an exception happens whlst running mark change queue status to ERROR, and log 
                    $errMsg = $e->getMessage();
                    $queueItem->getFlowControl()->setErrorMessage($errMsg);
                    $this->queueControl->updateStatus($queueItem, FlowControl::STATUS_ERROR);
                    $this->logForQueueItem($queueItem, $errMsg, Logger::ERROR);
                }

            }

            sleep($this->config['app']['sender_sleep']);

        }
    }

    /**
     * Return pathinfo of the staged resultset
     * @param $dataStageKey
     * @return string
     */
    private function getResultSetStagePathInfo($dataStageKey)
    {

        //Get path for file containing results
        $dir = rtrim($this->config['app']['runner_staging_dir'], '/') . '/';
        $filepath = $dir . $dataStageKey . '.csv';

        return pathinfo($filepath);

    }


    /**
     * Send email output to user
     * @param QueueItem $queueItem
     * @param OutputEmail $output
     */
    private function outputEmail(QueueItem $queueItem, OutputEmail $output)
    {

        $jobDefinition = $queueItem->getJobDefintion();

        //Start the message basics

        /**
         * @var $message \Swift_Message
         */
        $message = \Swift_Message::newInstance("Results from job \"{$jobDefinition->getName()}\"")
            ->setFrom(array('noreply@mail.boydstratton.com' => 'Autoq Sender'))
            ->setSender('noreply@mail.boydstratton.com')
            ->setTo($output->getEmail(), $output->getEmail())
            ->setBody($this->buildPlainTextBody($jobDefinition));


        $filepath = $this->resultsPathInfo['dirname'] . "/" . $this->resultsPathInfo['basename'];

        switch ($output->getStyle()) {
            case OutputEmail::STYLE_ATTACHMENT:
                $attachment = \Swift_Attachment::fromPath($filepath);
                $message->attach($attachment);
                break;
            case OutputEmail::STYLE_HTML:
                $resultData = $this->readRowsFromStagingFile(self::MAX_HTML_ROWS, $rowsRemain);

                $otherData = [

                    'rowsRemain' => $rowsRemain,
                    'schedule' => $jobDefinition->getScheduleOriginal()

                ];

                $markup = $this->buildHTML($resultData, $otherData);

                $message->addPart($markup, 'text/html');
                break;
            default:
                $this->logForQueueItem($queueItem, "Unknown output style {$output->getStyle()}", Logger::ERROR);
                break;

        }

        //Send the email
        $transport = \Swift_SmtpTransport::newInstance('postfix', 25);
        $mailer = \Swift_Mailer::newInstance($transport);
        $result = $mailer->send($message);

        if ($result == 0) {
            $this->logForQueueItem($queueItem, "There was a problem sending the email output to {$output->getEmail()}", Logger::ERROR);
        } else {
            $this->logForQueueItem($queueItem, "Email output sent to email address {$output->getEmail()}");
        }

    }

    /**
     * @param JobDefinition $jobDefinition
     * @return string
     */
    private function buildPlainTextBody(JobDefinition $jobDefinition)
    {

        $body = "Here are the results from job ID: {$jobDefinition->getId()} - {$jobDefinition->getName()}\n\n";

        $body .= "Schedule: {$jobDefinition->getScheduleOriginal()}\n\n";

        $body .= "Autoq\n\n";

        return $body;
    }

    /**
     * @param $data
     * @param $otherData
     * @return string
     */
    private function buildHTML($data, $otherData)
    {

        $headerRow = [];
        $dataRows = [];
        $noResultSet = true;

        if (count($data) > 0) {
            $noResultSet = false;
            $headerRow = array_shift($data);
            $dataRows = $data;
        }

        $templateData = $otherData;

        $templateData['noResultSet'] = $noResultSet;
        $templateData['headerRow'] = $headerRow;
        $templateData['dataRows'] = $dataRows;

        $html = $this->view->render("email/senderHtml", $templateData);

        return $html;

    }


    /**
     * Get rows from staging file used for HTML email
     * @param $limit
     * @param bool $rowsRemain
     * @return array
     * @throws \Exception
     */
    private function readRowsFromStagingFile($limit, &$rowsRemain = false)
    {

        $rows = [];

        $filepath = $this->resultsPathInfo['dirname'] . "/" . $this->resultsPathInfo['basename'];

        if (file_exists($filepath)) {

            if ($handle = fopen($filepath, 'r')) {

                $count = 0;
                $limit++; //Add extra line for headers

                while ($count < $limit && ($row = fgetcsv($handle))) {
                    $rows[] = $row;
                    $count++;
                }

                //Try and get read another row to see if more data is available and set flag accordingly
                if (fgetcsv($handle) === false) {
                    $rowsRemain = true;
                }

                fclose($handle);

            }
        } else {
            throw new \Exception("Unable to find file: $filepath");
        }

        return $rows;

    }


    /**
     * Deliver resultset to S3
     * @param QueueItem $queueItem
     * @param OutputS3 $output
     */
    private function outputS3(QueueItem $queueItem, OutputS3 $output)
    {

        $filepath = $this->resultsPathInfo['dirname'] . "/" . $this->resultsPathInfo['basename'];

        $config = $this->s3CredentialsService->getByAlias('default');

        $s3 = new S3Client([
            'version' => 'latest',
            'region' => $config['region'],

            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret']
            ],
        ]);

        //Either use user provided key or system one
        $bucketKey = $output->getKey() == "" ? $this->resultsPathInfo['basename'] : $output->getKey();

        $uploader = new MultipartUploader($s3, $filepath, [
            'bucket' => $output->getBucket(),
            'key' => $bucketKey,
        ]);

        $this->logForQueueItem($queueItem, "Uploading to bucket {$output->getBucket()} with key: $bucketKey");

        //Throws on error, caught in caller main loop
        $uploader->upload();

        $this->logForQueueItem($queueItem, "Upload successful");

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