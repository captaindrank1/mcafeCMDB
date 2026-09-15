<?php
/**
 * メールアドレス台帳：一覧表示のロジック部
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/06 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/../../../frames/logic/db_connection.php';

$mailRows = array();
$listError = '';

try {
    $db = cmdb_db();
    $mailRows = $db->ExecuteQuery(
        'SELECT user_full_name, mail_address, mobile_address FROM CMDB_CAT_MAIL ORDER BY user_full_name'
    );
} catch (\Exception $e) {
    $listError = 'データの取得に失敗しました。';
}

require_once __DIR__ . '/../views/list_view.php';
