<?php
/**
 * メールアドレス台帳：条件検索のロジック部
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

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$searched = ($keyword !== '');
$searchRows = array();
$searchError = '';

if ($searched) {
    try {
        $db = cmdb_db();
        $like = '%' . $keyword . '%';
        $searchRows = $db->ExecuteQuery(
            'SELECT user_full_name, mail_address, mobile_address FROM CMDB_CAT_MAIL WHERE user_full_name LIKE ? OR mail_address LIKE ? OR mobile_address LIKE ? ORDER BY user_full_name',
            array($like, $like, $like)
        );
    } catch (\Exception $e) {
        $searchError = '検索に失敗しました。';
    }
}

require_once __DIR__ . '/../views/search_view.php';
