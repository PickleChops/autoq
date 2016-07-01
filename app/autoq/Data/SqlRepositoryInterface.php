<?php
namespace Autoq\Data;
/**
 * Interface SqlRepositoryInterface
 */
interface SqlRepositoryInterface
{
    public function save($data);
    public function exists($id);
    public function getById($id);
    public function getAll();
    public function update($id, $data);
    public function delete($id);
    public function getWhere($whereString = null);
    public function getDBConnection();

}