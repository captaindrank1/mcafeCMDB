<?php
/**
 * MySqliDb.php
 * PDOを使ってMySQLにアクセスするためのクラス
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/06 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  Fundamentals
 * @package   Database
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

namespace Fundamentals\Database;

require_once __DIR__ . '/DbBase.php';

class PdoDb extends DbBase
{
    private $conn = null;


    /**
     * DB接続
     */
    public function Open()
    {
        try {

            $dsn =
            "mysql:host={$this->host};" .
            "dbname={$this->database};" .
            "charset=utf8mb4";


            $this->conn = new \PDO(
                $dsn,
                $this->user,
                $this->password,
                array(
                    \PDO::ATTR_ERRMODE =>
                    \PDO::ERRMODE_EXCEPTION
                )
                );


            $this->isOpen = true;


        } catch (\PDOException $e) {

            throw new \Exception(
                "DB接続エラー: " . $e->getMessage()
                );

        }
    }


    /**
     * DB切断
     */
    public function Close()
    {
        $this->CheckBeforeClose();

        $this->conn = null;
        $this->isOpen = false;
    }


    /**
     * トランザクション開始
     */
    public function BeginTransaction()
    {
        $this->CheckOpen();

        $this->conn->beginTransaction();

        $this->inTransaction = true;
    }


    /**
     * Commit
     */
    public function Commit()
    {
        $this->CheckOpen();

        if ($this->inTransaction) {

            $this->conn->commit();

            $this->inTransaction = false;
        }
    }


    /**
     * Rollback
     */
    public function Rollback()
    {
        $this->CheckOpen();

        if ($this->inTransaction) {

            $this->conn->rollBack();

            $this->inTransaction = false;
        }
    }


    /**
     * SELECT
     */
    public function ExecuteQuery($sql, $params = array())
    {
        $this->CheckOpen();

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(
            \PDO::FETCH_ASSOC
            );
    }


    /**
     * 1件取得
     */
    public function ExecuteSingle($sql, $params = array())
    {
        $rows = $this->ExecuteQuery(
            $sql,
            $params
            );


        if (count($rows) == 0) {

            return null;

        }


        return $rows[0];
    }


    /**
     * 単一値取得
     */
    public function ExecuteScalar($sql, $params = array())
    {
        $this->CheckOpen();

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchColumn();
    }


    /**
     * INSERT UPDATE DELETE
     */
    public function ExecuteNonQuery($sql, $params = array())
    {
        $this->CheckOpen();

        $stmt = $this->conn->prepare($sql);

        $stmt->execute($params);

        return $stmt->rowCount();
    }
}
