<?php

namespace Autoq\Data;

use Phalcon\Db;
use Phalcon\Db\Adapter;

trait DataTraits
{
    
    /**
     * @param $connection
     * @param $table
     * @param $whereString
     * @param $orderString
     * @param $limitString
     * @param callable $hydrater
     * @return array|bool
     */
    protected function simpleSelect(Adapter $connection, $table, $whereString, $orderString, $limitString, Callable $hydrater = null)
    {

        $whereString = $whereString == '' ? null : 'WHERE ' . $whereString;
        $orderString = $orderString == '' ? null : 'ORDER BY ' . $orderString;
        $limitString = $limitString == '' ? null : 'LIMIT ' . $limitString;

        $result = $connection->fetchAll("SELECT * FROM `$table` $whereString $orderString $limitString", Db::FETCH_ASSOC);


        $rows = [];

        if ($hydrater !== null) {
            foreach ($result as $row) {
                $rows[] = call_user_func($hydrater, $row);
            }
        }

        return $rows;

    }


}