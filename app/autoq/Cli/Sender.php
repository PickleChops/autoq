<?php

namespace Autoq\Cli;

use Autoq\Data\Jobs\JobDefinition;
use Autoq\Data\Jobs\OutputEmail;
use Autoq\Data\Queue\QueueControl;
use Autoq\Data\Queue\FlowControl;
use Autoq\Data\Queue\QueueItem;
use Autoq\Services\DbConnectionMgr;
use Phalcon\Config;
use Phalcon\Db\Adapter;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;

class Sender implements CliTask
{

    protected $config;
    protected $log;
    protected $queueControl;
    private $dbConnectionMgr;

    /**
     * Sender constructor.
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
        $this->log->info("Sender started");

        while (true) {

            /**
             * @var $queueItem QueueItem
             */
            if (($queueItem = $this->queueControl->grabNextToSend()) !== false) {

                try {

                    $jobDefinition = $queueItem->getJobDefintion();

                    $this->logForQueueItem($queueItem, "{$jobDefinition->countOutputs()} outputs defined in job");

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

            } else {
                $this->log->debug("No queue items ready to send");
            }

            sleep($this->config['app']['sender_sleep']);

        }
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
        $message = \Swift_Message::newInstance("Autoq: Output from job \"{$jobDefinition->getName()}\"")
            ->setFrom(array('autoq@localdev' => 'Autoq'))
            ->setTo($output->getEmail(), $output->getEmail())
            ->setBody("Here are the results from job ID: {$jobDefinition->getId()} - {$jobDefinition->getName()}");


        //Get path for file containing results
        $dir = rtrim($this->config['app']['runner_staging_dir'], '/') . '/';
        $filepath = $dir . $queueItem->getDataStageKey() . '.csv';
        

        switch ($output->getStyle()) {
            case OutputEmail::STYLE_ATTACHMENT:
                $attachment = \Swift_Attachment::fromPath($filepath);
                $message->attach($attachment);
                break;
            case OutputEmail::STYLE_HTML:
                
                
                break;
            default:
                $this->logForQueueItem($queueItem, "Unknown output style {$output->getStyle()}", Logger::ERROR);
                break;

        }

        //Send the email

        $transport = \Swift_SmtpTransport::newInstance('postfix', 25)
            ->setUsername('email')
            ->setPassword('password');


        $mailer = \Swift_Mailer::newInstance($transport);
        $result = $mailer->send($message);
        
        if($result == 0) {
            $this->logForQueueItem($queueItem,"There was a problem sending the email output to {$output->getEmail()}", Logger::ERROR);
        } else {
            $this->logForQueueItem($queueItem,"Email output sent to email address {$output->getEmail()}");
        }

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