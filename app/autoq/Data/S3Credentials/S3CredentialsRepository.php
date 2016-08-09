<?php

namespace Autoq\Data\S3Credentials;

use Autoq\Data\BaseRepository;
use Autoq\Data\S3Credentials\S3Credential;
use Phalcon\Db;
use Phalcon\Db\Exception;

class S3CredentialsRepository extends BaseRepository
{
    /**
     * @param $data
     * @return bool
     */
    public function save($data)
    {

        if ($this->dBConnection->insertAsDict('s3_credentials', $data) === false) {
            $this->log->error("Unable to save s3 credentials");
            return false;
        }

        return $this->dBConnection->lastInsertId();

    }

    /**
     * Fetch by id
     * @param $id
     * @return S3Credential
     */
    public function getById($id)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT * FROM s3_credentials where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch s3 credentials with id: $id");
            return false;
        }

        return $row === false ? [] : $this->hydrate($row);

    }

    /**
     * @param $alias
     * @return array|S3Credential|bool
     */
    public function getByAlias($alias)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT * FROM s3_credentials where alias = :alias", Db::FETCH_ASSOC, ['alias' => $alias]);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch s3 credentials with alias: $alias");
            return false;
        }

        return $row === false ? [] : $this->hydrate($row);
    }

    
    /**
     * @param null $limit
     * @return array|bool
     */
    public function getAll($limit = null)
    {
        try {

            $results = $this->simpleSelect($this->dBConnection, 's3_credentials', null, null, $limit, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch s3 credentials.");
            return false;
        }

        return $results;
    }

    /**
     * @param null $whereString
     * @return array|bool
     */
    public function getWhere($whereString = null)
    {
        try {

            $jobs = $this->simpleSelect($this->dBConnection, 's3_credentials', $whereString, null, null, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch s3_credentials with condition: $whereString");
            return false;
        }

        return $jobs;

    }

    /**
     * Delete a s3_credential
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {

            $this->dBConnection->execute("DELETE FROM s3_credentials where id = :id", ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to delete s3_credentials with id: $id");
            return false;
        }

        return true;
    }

    /**
     * update a s3_credential
     * @param $id
     * @param $data
     * @return bool
     */
    public function update($id, $data)
    {

        try {
            $this->dBConnection->updateAsDict('s3_credentials', $data, "id = $id");

        } catch (Exception $e) {
            $this->log->error("Unable to update s3_credentials with id: $id");
            return false;
        }

        return true;
    }

    /**
     * Does a record exist for this id
     * @param $id
     * @return array
     */
    public function exists($id)
    {
        try {

            $row = $this->dBConnection->fetchOne("SELECT id FROM s3_credentials where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to read s3_credentials with id: $id");
            return false;
        }

        return $row ? array_key_exists('id', $row) : false;

    }

    /**
     * Hydrate a S3 Credential object
     * @param $row
     * @return DbCredential
     */
    protected function hydrate($row)
    {
        $dbCred = new S3Credential($row);

        return $dbCred;
    }

}