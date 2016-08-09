<?php

namespace Autoq\Services;


use Phalcon\Config;
use Phalcon\Http\Response;
use Autoq\Data\S3Credentials\S3CredentialsRepository;
use Autoq\Data\S3Credentials\S3Credential;

class S3CredentialsService
{
    const DEFAULT_SENDER_S3 = 'default_sender_s3';

    /**
     * @var $s3CredentialsRepository S3CredentialsRepository
     */
    private $s3CredentialsRepository;

    /**
     * @var Config
     */
    private $config;


    /**
     * DbCredentialsService constructor.
     * @param Config $config
     * @param  $s3CredentialsRepository
     */
    public function __construct(Config $config, S3CredentialsRepository $s3CredentialsRepository)
    {
        $this->s3CredentialsRepository = $s3CredentialsRepository;
        $this->config = $config;
    }

    /**
     * Return credential set by alias
     * @param $alias
     * @return string
     * @throws \Exception
     */
    public function getByAlias($alias)
    {

        if (($data = $this->s3CredentialsRepository->getByAlias($alias)) instanceof S3Credential) {

            $config = $data->asConfigArray();

        } else {

            if ($alias == 'default' && isset($this->config[self::DEFAULT_SENDER_S3])) {

                $config = (array)$this->config[self::DEFAULT_SENDER_S3];

            } else {
                throw new \Exception("Unable to find the default runner s3 config for alias: " . $alias);
            }

        }

        return $config;
    }

    /**
     * @return array List of aliases
     */
    public function getAliases() {
        
        $aliases = ['default']; //Default always permitted
        
        $data = $this->s3CredentialsRepository->getAll();

        /**
         * @var $row S3Credential
         */
        foreach($data as $row) {
            $aliases[] = $row->getAlias();
        }


        return $aliases;
    }

}