<?php
/**
 * リクエスト内で共有する DB 接続
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/15 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/global_config.php';
require_once __DIR__ . '/../../commonLib/Fundamentals/Database/MySqliDb.php';

use Fundamentals\Database\MySqliDb;

/**
 * 共有 DB 接続を返す
 *
 * 初回呼び出しで接続を開き、以降は同じ接続を返す。
 * 接続はリクエスト終了時に自動で閉じる。
 *
 * @return MySqliDb
 */
function cmdb_db()
{
    static $db = null;

    if ($db === null || !$db->IsOpen()) {
        $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $db->Open();

        register_shutdown_function(function () use ($db) {
            if ($db->IsOpen()) {
                $db->Close();
            }
        });
    }

    return $db;
}
