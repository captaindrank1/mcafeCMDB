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

require_once __DIR__ . '/../../../frames/logic/db_connection.php';

$keyword = isset($_GET['keyword']) ? trim((string)$_GET['keyword']) : '';
$searched = isset($_GET['keyword']);
$searchError = '';
$searchResults = array();

if ($searched) {
    $like = '%' . $keyword . '%';

    try {
        $db = cmdb_db();

        $sql = <<<SQL
SELECT
    p.primary_account,
    p.user_name,
    p.user_category,
    p.remarks,
    p.update_time
FROM
    CMDB_CAT_PRIMARY_ACCOUNTS p
WHERE
    p.deleted_flag = 0
    AND (
            p.primary_account LIKE ?
        OR  p.user_name       LIKE ?
        OR  p.user_category   LIKE ?
        OR  p.remarks         LIKE ?
        OR  EXISTS (
                SELECT 1
                FROM CMDB_CAT_ACCOUNT_LIST a
                WHERE a.primary_account = p.primary_account
                  AND a.deleted_flag = 0
                  AND (
                          a.service_name LIKE ?
                      OR  a.id           LIKE ?
                      OR  a.passwd       LIKE ?
                      OR  a.mail_address LIKE ?
                  )
            )
        OR  EXISTS (
                SELECT 1
                FROM CMDB_CAT_USE_MUA m
                WHERE m.primary_account = p.primary_account
                  AND m.deleted_flag = 0
                  AND m.mua LIKE ?
            )
    )
ORDER BY
    p.primary_account
SQL;

        $primaryRows = $db->ExecuteQuery(
            $sql,
            array($like, $like, $like, $like, $like, $like, $like, $like, $like)
        );

        $servicesByAccount = array();
        $muasByAccount = array();

        if (count($primaryRows) > 0) {
            $accounts = array();
            foreach ($primaryRows as $primary) {
                $accounts[] = $primary['primary_account'];
            }
            $placeholders = implode(', ', array_fill(0, count($accounts), '?'));

            $sqlService = <<<SQL
SELECT
    primary_account,
    service_name,
    id,
    passwd,
    mail_address
FROM
    CMDB_CAT_ACCOUNT_LIST
WHERE
    primary_account IN ({$placeholders})
    AND deleted_flag = 0
    AND (
            service_name LIKE ?
        OR  id           LIKE ?
        OR  passwd       LIKE ?
        OR  mail_address LIKE ?
    )
ORDER BY
    primary_account,
    service_name
SQL;

            $serviceRows = $db->ExecuteQuery(
                $sqlService,
                array_merge($accounts, array($like, $like, $like, $like))
            );
            foreach ($serviceRows as $row) {
                $servicesByAccount[$row['primary_account']][] = $row;
            }

            $sqlMua = <<<SQL
SELECT
    primary_account,
    mua
FROM
    CMDB_CAT_USE_MUA
WHERE
    primary_account IN ({$placeholders})
    AND deleted_flag = 0
    AND mua LIKE ?
ORDER BY
    primary_account,
    mua
SQL;

            $muaRows = $db->ExecuteQuery(
                $sqlMua,
                array_merge($accounts, array($like))
            );
            foreach ($muaRows as $row) {
                $muasByAccount[$row['primary_account']][] = $row;
            }
        }

        foreach ($primaryRows as $primary) {
            $key = $primary['primary_account'];
            $searchResults[] = array(
                'primary'  => $primary,
                'services' => isset($servicesByAccount[$key]) ? $servicesByAccount[$key] : array(),
                'muas'     => isset($muasByAccount[$key]) ? $muasByAccount[$key] : array(),
            );
        }
    } catch (\Exception $e) {
        $searchError = '検索に失敗しました。';
    }
}

require_once __DIR__ . '/../views/search_view.php';
