<?php

namespace Autoq\Data\DbCredentials;

use Autoq\Data\BaseRepository;
use Phalcon\Db;
use Phalcon\Db\Exception;

class DbCredentialsRepository extends BaseRepository
{
    /**
     * @param $data
     * @return bool
     */
    public function save($data)
    {

        if ($this->dBConnection->insertAsDict('db_credentials', $data) === false) {
            $this->log->error("Unable to save db credentials");
            return false;
        }

        return $this->dBConnection->lastInsertId();

    }

    /**
     * Fetch by id
     * @param $id
     * @return DbCredential
     */
    public function getById($id)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT * FROM db_credentials where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch db credentials with id: $id");
            return false;
        }

        return $row === false ? [] : $this->hydrate($row);

    }

    /**
     * @param $alias
     * @return array|DbCredential|bool
     */
    public function getByAlias($alias)
    {

        try {

            $row = $this->dBConnection->fetchOne("SELECT * FROM db_credentials where alias = :alias", Db::FETCH_ASSOC, ['alias' => $alias]);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch db credentials with alias: $alias");
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

            $results = $this->simpleSelect($this->dBConnection, 'db_credentials', null, null, $limit, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch db credentials.");
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

            $jobs = $this->simpleSelect($this->dBConnection, 'db_credentials', $whereString, null, null, [$this, 'hydrate']);

        } catch (Exception $e) {
            $this->log->error("Unable to fetch db_credentials with condition: $whereString");
            return false;
        }

        return $jobs;

    }

    /**
     * Delete a db_credential
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        try {

            $this->dBConnection->execute("DELETE FROM db_credentials where id = :id", ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to delete db_credentials with id: $id");
            return false;
        }

        return true;
    }

    /**
     * update a db_credential
     * @param $id
     * @param $data
     * @return bool
     */
    public function update($id, $data)
    {

        try {
            $this->dBConnection->updateAsDict('db_credentials', $data, "id = $id");

        } catch (Exception $e) {
            $this->log->error("Unable to update db_credentials with id: $id");
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

            $row = $this->dBConnection->fetchOne("SELECT id FROM db_credentials where id = :id", Db::FETCH_ASSOC, ['id' => $id]);

        } catch (Exception $e) {
            $this->log->error("Unable to read db_credentials with id: $id");
            return false;
        }

        return $row ? array_key_exists('id', $row) : false;

    }

    /**
     * Hydrate a Db Credential object
     * @param $row
     * @return DbCredential
     */
    protected function hydrate($row)
    {
        $dbCred = new DbCredential($row);

        return $dbCred;
    }

}