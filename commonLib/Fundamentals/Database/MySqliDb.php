<?php
/**
 * MySqliDb.php
 * MySQLiを使ってMySQLにアクセスするためのクラス
 *
 * PHP version 5.4.16
 *
 * @category  Fundamentals
 * @package   Database
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

namespace Fundamentals\Database;

require_once __DIR__ . '/DbBase.php';

class MySqliDb extends DbBase
{
    private $conn = null;



    public function Open()
    {
        $this->conn = new \mysqli(
            $this->host,
            $this->user,
            $this->password,
            $this->database
            );


        if ($this->conn->connect_errno) {

            throw new \Exception(
                "DB接続エラー: " .
                $this->conn->connect_error
                );
        }


        $this->conn->set_charset(
            "utf8mb4"
            );


        $this->isOpen = true;
    }



    public function Close()
    {
        $this->CheckBeforeClose();

        if ($this->conn !== null) {

            $this->conn->close();

        }

        $this->conn = null;
        $this->isOpen = false;
    }



    public function BeginTransaction()
    {
        $this->CheckOpen();

        $this->conn->autocommit(false);
        $this->conn->query('START TRANSACTION');

        $this->inTransaction = true;
    }



    public function Commit()
    {
        $this->CheckOpen();

        if ($this->inTransaction) {

            $this->conn->commit();
            $this->conn->autocommit(true);

            $this->inTransaction = false;
        }
    }



    public function Rollback()
    {
        $this->CheckOpen();

        if ($this->inTransaction) {

            $this->conn->rollback();
            $this->conn->autocommit(true);

            $this->inTransaction = false;
        }
    }



    public function ExecuteQuery($sql, $params = array())
    {
        $this->CheckOpen();

        $stmt = $this->Prepare(
            $sql,
            $params
            );

        $stmt->execute();

        return $this->FetchRows($stmt);
    }



    public function ExecuteSingle($sql, $params = array())
    {
        $rows = $this->ExecuteQuery(
            $sql,
            $params
            );


        return count($rows)
            ? $rows[0]
            : null;
    }



    public function ExecuteScalar($sql, $params = array())
    {
        $rows = $this->ExecuteQuery(
            $sql,
            $params
            );


        if (count($rows) == 0) {

            return null;
        }


        $first = array_values($rows[0]);
        return $first[0];
    }



    public function ExecuteNonQuery($sql, $params = array())
    {
        $this->CheckOpen();


        $stmt = $this->Prepare(
            $sql,
            $params
            );


        $stmt->execute();


        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected;
    }



    /**
     * mysqli用パラメータ処理
     */
    private function Prepare($sql, &$params)
    {
        $stmt = $this->conn->prepare($sql);


        if (!$stmt) {

            throw new \Exception(
                $this->conn->error
                );
        }


        if (count($params) > 0) {

            $types = "";

            foreach ($params as $p) {

                $types .= is_int($p)
                    ? "i"
                    : "s";
            }


            $refs = array();

            foreach ($params as $key => $value) {

                $refs[$key] = &$params[$key];
            }


            array_unshift(
                $refs,
                $types
                );


            call_user_func_array(
                array($stmt, 'bind_param'),
                $refs
                );
        }


        return $stmt;
    }



    private function FetchRows($stmt)
    {
        $stmt->store_result();

        $meta = $stmt->result_metadata();
        $fields = $meta->fetch_fields();
        $meta->free();

        $values = array();
        $refs = array();

        foreach ($fields as $field) {

            $values[$field->name] = null;
            $refs[] = &$values[$field->name];
        }


        call_user_func_array(
            array($stmt, 'bind_result'),
            $refs
            );


        $rows = array();

        while ($stmt->fetch()) {

            $row = array();

            foreach ($values as $key => $value) {

                $row[$key] = $value;
            }

            $rows[] = $row;
        }


        $stmt->free_result();
        $stmt->close();

        return $rows;
    }
}
