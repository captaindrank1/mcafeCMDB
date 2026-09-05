<?php
/**
 * メールアドレス台帳：条件検索のロジック部
 *
 * PHP version 5.4.16
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/../../../frames/logic/global_config.php';
require_once __DIR__ . '/../../../commonLib/Fundamentals/Database/MySqliDb.php';
use Fundamentals\Database\MySqliDb;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$searched = ($keyword !== '');
$searchRows = array();
$searchError = '';

if ($searched) {
    try {
        $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $db->Open();
        $like = '%' . $keyword . '%';
        $searchRows = $db->ExecuteQuery(
            'SELECT user_full_name, mail_address, mobile_address FROM CMDB_CAT_MAIL WHERE user_full_name LIKE ? OR mail_address LIKE ? OR mobile_address LIKE ? ORDER BY user_full_name',
            array($like, $like, $like)
        );
        $db->Close();
    } catch (\Exception $e) {
        $searchError = '検索に失敗しました。';
    }
}

require_once __DIR__ . '/../views/search_view.php';
