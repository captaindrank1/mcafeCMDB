<?php
/**
 * アカウント管理台帳：一覧表示のロジック部
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/10 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/../../../frames/logic/db_connection.php';

$accountRows = array();
$listError = '';

try {
    $db = cmdb_db();

    $sql = <<<SQL
SELECT
    primary_account,
    user_name,
    user_category,
    remarks,
    update_time
FROM
    CMDB_CAT_PRIMARY_ACCOUNTS
WHERE
    deleted_flag = 0
ORDER BY
    primary_account
SQL;

    $accountRows = $db->ExecuteQuery($sql);
} catch (\Exception $e) {
    $listError = '一覧の取得に失敗しました。';
}

require_once __DIR__ . '/../views/list_view.php';
