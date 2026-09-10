<?php
/**
 * アカウント管理台帳：条件検索のロジック部
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

require_once __DIR__ . '/../../../frames/logic/global_config.php';
require_once __DIR__ . '/../../../commonLib/Fundamentals/Database/MySqliDb.php';
use Fundamentals\Database\MySqliDb;

$keyword = isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : '';
$searched = isset($_GET['keyword']);
$searchError = '';
$searchResults = array();

if ($searched) {
    $like = '%' . $keyword . '%';

    try {
        $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
        $db->Open();

        $sql = <<<SQL
SELECT DISTINCT
    p.primary_account,
    p.user_name,
    p.user_category,
    p.remarks,
    p.update_time
FROM
    CMDB_CAT_PRIMARY_ACCOUNTS p
LEFT JOIN
    CMDB_CAT_ACCOUNT_LIST a
        ON  a.primary_account = p.primary_account
        AND a.deleted_flag = 0
LEFT JOIN
    CMDB_CAT_USE_MUA m
        ON  m.primary_account = p.primary_account
        AND m.deleted_flag = 0
WHERE
    p.deleted_flag = 0
    AND (
            p.primary_account LIKE ?
        OR  p.user_name       LIKE ?
        OR  p.user_category   LIKE ?
        OR  p.remarks         LIKE ?
        OR  a.service_name    LIKE ?
        OR  a.id              LIKE ?
        OR  a.passwd          LIKE ?
        OR  a.mail_address    LIKE ?
        OR  m.mua             LIKE ?
    )
ORDER BY
    p.primary_account
SQL;

        $primaryRows = $db->ExecuteQuery(
            $sql,
            array($like, $like, $like, $like, $like, $like, $like, $like, $like)
        );

        $sqlService = <<<SQL
SELECT
    service_name,
    id,
    passwd,
    mail_address
FROM
    CMDB_CAT_ACCOUNT_LIST
WHERE
    primary_account = ?
    AND deleted_flag = 0
    AND (
            service_name LIKE ?
        OR  id           LIKE ?
        OR  passwd        LIKE ?
        OR  mail_address LIKE ?
    )
ORDER BY
    service_name
SQL;

        $sqlMua = <<<SQL
SELECT
    mua
FROM
    CMDB_CAT_USE_MUA
WHERE
    primary_account = ?
    AND deleted_flag = 0
    AND mua LIKE ?
ORDER BY
    mua
SQL;

        foreach ($primaryRows as $primary) {
            $services = $db->ExecuteQuery(
                $sqlService,
                array($primary['primary_account'], $like, $like, $like, $like)
            );
            $muas = $db->ExecuteQuery(
                $sqlMua,
                array($primary['primary_account'], $like)
            );

            $searchResults[] = array(
                'primary'  => $primary,
                'services' => $services,
                'muas'     => $muas,
            );
        }

        $db->Close();
    } catch (\Exception $e) {
        $searchError = '検索に失敗しました。';
    }
}

require_once __DIR__ . '/../views/search_view.php';
