<?php
/**
 * ダッシュボードのロジック部
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

session_start();

require_once __DIR__ . '/../../commonLib/Fundamentals/Database/MySqliDb.php';
require_once __DIR__ . '/global_config.php';
use Fundamentals\Database\MySqliDb;

if (!isset($_SESSION['current_user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['current_user'];

$infoFile = __DIR__ . '/../../information.txt';
$information = file_exists($infoFile) ? file_get_contents($infoFile) : 'information.txt が見つかりません。';

$catalogs = array();
$catalogFunctions = array();

try {
    $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
    $db->Open();

    $catalogs = $db->ExecuteQuery('SELECT catalog_id, catalog_name, working_dir FROM CMDB_M_CATALOGS WHERE invalid = 0 ORDER BY display_order');
    $functions = $db->ExecuteQuery('SELECT catalog_id, display_order, function_name, page_controller, page_parameter FROM CMDB_M_CATALOG_FUNCTIONS ORDER BY catalog_id, display_order');
    $db->Close();

    foreach ($functions as $function) {
        $catalogFunctions[$function['catalog_id']][] = $function;
    }
} catch (\Exception $e) {
    $catalogs = array();
    $catalogFunctions = array();
}

$currentCatalogId = isset($_GET['catalog_id']) ? (int)$_GET['catalog_id'] : 0;
$currentFunc = isset($_GET['func']) ? basename((string)$_GET['func']) : '';
$embedHtml = null;

if ($currentCatalogId > 0 && $currentFunc !== '') {

    $currentCatalog = null;
    foreach ($catalogs as $catalog) {
        if ((int)$catalog['catalog_id'] === $currentCatalogId) {
            $currentCatalog = $catalog;
            break;
        }
    }

    $currentFunction = null;
    if ($currentCatalog !== null && isset($catalogFunctions[$currentCatalogId])) {
        foreach ($catalogFunctions[$currentCatalogId] as $function) {
            if ($function['page_controller'] === $currentFunc) {
                $currentFunction = $function;
                break;
            }
        }
    }

    if ($currentCatalog !== null && $currentFunction !== null) {
        $currentCatalogName = (string)$currentCatalog['catalog_name'];
        $workingDir = ltrim($currentCatalog['working_dir'], '/\\');
        $controllerPath = __DIR__ . '/../../' . $workingDir . '/' . $currentFunction['page_controller'];
        $catalogsRoot = realpath(__DIR__ . '/../../catalogs');
        $real = realpath($controllerPath);

        if ($real !== false && strpos($real, $catalogsRoot) === 0) {

            $pageParams = array();
            $declared = trim($currentFunction['page_parameter']);
            if ($declared !== '') {
                foreach (explode('&', $declared) as $pair) {
                    $kv = explode('=', $pair, 2);
                    if (count($kv) === 2 && isset($_GET[$kv[0]])) {
                        $pageParams[$kv[0]] = ($kv[1] === '%i')
                            ? (int)$_GET[$kv[0]]
                            : (string)$_GET[$kv[0]];
                    }
                }
            }

            $embedBaseUrl = 'dashboard.php?catalog_id=' . $currentCatalogId . '&func=';
            $embedUrl = $embedBaseUrl . rawurlencode($currentFunc);

            ob_start();
            try {
                require $real;
            } catch (\Exception $e) {
                echo '<div class="callout callout-danger"><p>画面の読み込みに失敗しました。</p></div>';
            }
            $embedHtml = ob_get_clean();

        } else {
            $embedHtml = '<div class="callout callout-warning"><p>指定された機能が見つかりません。</p></div>';
        }
    } else {
        $embedHtml = '<div class="callout callout-warning"><p>指定された機能が見つかりません。</p></div>';
    }
}

require_once __DIR__ . '/../views/dashboard_view.php';
