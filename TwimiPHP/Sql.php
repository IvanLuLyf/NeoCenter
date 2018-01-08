<?php

/**
 * Created by PhpStorm.
 * User: IvanLu
 * Date: 2018/1/1
 * Time: 15:07
 */
class Sql
{
    protected $table;
    private $filter = '';
    private $join = '';
    private $param = array();

    public function where($where = array(), $param = array())
    {
        if ($where) {
            $this->filter .= ' WHERE ';
            $this->filter .= implode(' ', $where);
            $this->param = $param;
        }
        return $this;
    }

    public function join($tableName, $condition = array(), $mod = '')
    {
        $this->join .= " $mod JOIN `$tableName`";
        if ($condition) {
            $this->join .= " ON (" . implode(' ', $condition) . ") ";
        }
        return $this;
    }

    public function limit($size, $start = 0)
    {
        $this->filter .= " LIMIT $start,$size ";
        return $this;
    }

    public function order($order = array())
    {
        if ($order) {
            $this->filter .= ' ORDER BY ';
            $this->filter .= implode(',', $order);
        }
        return $this;
    }

    public function fetchAll($column = '*')
    {
        $sql = sprintf("select %s from `%s` %s %s", $column, $this->table, $this->join, $this->filter);
        $sth = Database::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->join = '';
        $this->filter = '';
        $this->param = array();
        return $sth->fetchAll();
    }

    public function fetch($column = '*')
    {
        $sql = sprintf("select %s from `%s` %s %s", $column, $this->table, $this->join, $this->filter);
        $sth = Database::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->join = '';
        $this->filter = '';
        $this->param = array();
        return $sth->fetch();
    }

    public function delete()
    {
        $sql = sprintf("delete from `%s` where %s", $this->table, $this->filter);
        $sth = Database::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->join = '';
        $this->filter = '';
        $this->param = array();
        return $sth->rowCount();
    }

    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s", $this->table, $this->formatInsert($data));
        $sth = Database::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->join = '';
        $this->filter = '';
        $this->param = array();
        return Database::pdo()->lastInsertId();
    }

    public function update($data)
    {
        $sql = sprintf("update `%s` set %s %s", $this->table, $this->formatUpdate($data), $this->filter);
        $sth = Database::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();
        $this->join = '';
        $this->filter = '';
        $this->param = array();
        return $sth->rowCount();
    }

    public function formatParam(PDOStatement $sth, $params = array())
    {
        foreach ($params as $param => &$value) {
            $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
            $sth->bindParam($param, $value);
        }
        return $sth;
    }

    private function formatInsert($data)
    {
        $fields = array();
        $names = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $names[] = sprintf(":%s", $key);
        }
        $field = implode(',', $fields);
        $name = implode(',', $names);
        return sprintf("(%s) values (%s)", $field, $name);
    }

    private function formatUpdate($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = :%s", $key, $key);
        }
        return implode(',', $fields);
    }
}