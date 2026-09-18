<?php
/**
 * 認証確認とサイドバーメニュー(台帳・機能)の取得
 *
 * PHP version 5.4.16
 *
 * 読み込み後に以下の変数が設定される。
 *   $user             : ログインユーザー
 *   $catalogs         : CMDB_M_CATALOGS の有効行
 *   $catalogFunctions : catalog_id => CMDB_M_CATALOG_FUNCTIONS 行の配列
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : dashboard_logic.php から分離
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

session_start();

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/perf_log.php';

cmdb_perf_mark('session');

if (!isset($_SESSION['current_user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['current_user'];

$catalogs = array();
$catalogFunctions = array();

if (isset($_SESSION['menu_cache'])
    && isset($_SESSION['menu_cache']['expires'])
    && $_SESSION['menu_cache']['expires'] > time()
) {
    $catalogs = $_SESSION['menu_cache']['catalogs'];
    $catalogFunctions = $_SESSION['menu_cache']['functions'];
} else {
    try {
        $db = cmdb_db();
        cmdb_perf_mark('db_connect');

        $catalogs = $db->ExecuteQuery('SELECT catalog_id, catalog_name, working_dir FROM CMDB_M_CATALOGS WHERE invalid = 0 ORDER BY display_order');
        $functions = $db->ExecuteQuery('SELECT catalog_id, display_order, function_name, page_controller, page_parameter FROM CMDB_M_CATALOG_FUNCTIONS ORDER BY catalog_id, display_order');

        foreach ($functions as $function) {
            $catalogFunctions[$function['catalog_id']][] = $function;
        }

        $_SESSION['menu_cache'] = array(
            'expires' => time() + MENU_CACHE_TTL,
            'catalogs' => $catalogs,
            'functions' => $catalogFunctions,
        );
    } catch (\Exception $e) {
        $catalogs = array();
        $catalogFunctions = array();
    }
}

cmdb_perf_mark('menu');

/**
 * 親ページのURLを組み立てる
 *
 * @param string $page      親ページ名('search' | 'list' | 'register')
 * @param int    $catalogId 台帳ID
 * @param array  $params    追加クエリ(action, pk など)
 * @return string
 */
function cmdb_page_url($page, $catalogId, $params = array())
{
    $query = array_merge(array('catalog_id' => (int)$catalogId), $params);
    return $page . '.php?' . http_build_query($query);
}
