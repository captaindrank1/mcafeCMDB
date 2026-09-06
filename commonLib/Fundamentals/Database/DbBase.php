<?php
/**
 * DbBase.php
 * 接続先のRDBMSが何であっても、アプリケーション側には同じインターフェースを提供するためのラッパー用基底クラス
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

abstract class DbBase
{
    // 接続情報
    protected $host;
    protected $database;
    protected $user;
    protected $password;

    // 接続状態
    protected $isOpen = false;

    // トランザクション状態
    protected $inTransaction = false;


    /**
     * コンストラクタ
     */
    public function __construct($host, $database, $user, $password)
    {
        $this->host     = $host;
        $this->database = $database;
        $this->user     = $user;
        $this->password = $password;
    }


    /**
     * DB接続
     */
    abstract public function Open();


    /**
     * DB切断
     */
    abstract public function Close();


    /**
     * トランザクション開始
     */
    abstract public function BeginTransaction();


    /**
     * Commit
     */
    abstract public function Commit();


    /**
     * Rollback
     */
    abstract public function Rollback();


    /**
     * SELECT系
     */
    abstract public function ExecuteQuery($sql, $params = array());


    /**
     * 1件取得
     */
    abstract public function ExecuteSingle($sql, $params = array());


    /**
     * 単一値取得
     */
    abstract public function ExecuteScalar($sql, $params = array());


    /**
     * INSERT UPDATE DELETE
     */
    abstract public function ExecuteNonQuery($sql, $params = array());



    /**
     * 接続確認
     */
    public function IsOpen()
    {
        return $this->isOpen;
    }


    /**
     * トランザクション確認
     */
    public function IsInTransaction()
    {
        return $this->inTransaction;
    }


    /**
     * SQL実行前チェック
     */
    protected function CheckOpen()
    {
        if (!$this->isOpen) {

            throw new \Exception(
                "データベースに接続されていません。"
            );

        }
    }


    /**
     * Close前処理
     *
     * 未CommitならRollbackする
     */
    protected function CheckBeforeClose()
    {
        if ($this->inTransaction) {

            $this->Rollback();

        }
    }
}
