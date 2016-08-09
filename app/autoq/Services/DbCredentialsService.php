<?php

namespace Autoq\Services;


use Autoq\Data\DbCredentials\DbCredential;
use Autoq\Data\DbCredentials\DbCredentialsRepository;
use Phalcon\Config;
use Phalcon\Http\Response;

class DbCredentialsService
{
    const DEFAULT_RUNNER_DB = 'default_runner_db';

    /**
     * @var DbCredentialsRepository
     */
    private $dbCredentialsRepository;

    /**
     * @var Config
     */
    private $config;


    /**
     * DbCredentialsService constructor.
     * @param Config $config
     * @param DbCredentialsRepository $dbCredentialsRepository
     */
    public function __construct(Config $config, DbCredentialsRepository $dbCredentialsRepository)
    {
        $this->dbCredentialsRepository = $dbCredentialsRepository;
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

        if (($data = $this->dbCredentialsRepository->getByAlias($alias)) instanceof DbCredential) {

            $config = $data->asConfigArray();

        } else {

            if ($alias == 'default' && isset($this->config[self::DEFAULT_RUNNER_DB])) {

                $config = (array)$this->config[self::DEFAULT_RUNNER_DB];

            } else {
                throw new \Exception("Unable to find the default runner database config for alias: " . $alias);
            }

        }

        return $config;
    }

    /**
     * @return array List of aliases
     */
    public function getAliases() {
        
        $aliases = ['default']; //Default always permitted
        
        $data = $this->dbCredentialsRepository->getAll();

        /**
         * @var $row DbCredential
         */
        foreach($data as $row) {
            $aliases[] = $row->getAlias();
        }


        return $aliases;
    }

}