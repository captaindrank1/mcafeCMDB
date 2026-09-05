<?php
/**
 * メールアドレス台帳：一覧表示のロジック部
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

$mailRows = array();
$listError = '';

try {
    $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    $db->Open();
    $mailRows = $db->ExecuteQuery(
        'SELECT user_full_name, mail_address, mobile_address FROM CMDB_CAT_MAIL ORDER BY user_full_name'
    );
    $db->Close();
} catch (\Exception $e) {
    $listError = 'データの取得に失敗しました。';
}

require_once __DIR__ . '/../views/list_view.php';
