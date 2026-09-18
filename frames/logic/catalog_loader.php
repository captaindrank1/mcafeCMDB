<?php
/**
 * 台帳機能ページ(search / list / register)の共通ロジック部
 *
 * PHP version 5.4.16
 *
 * 呼び出し元(親ページのロジック)で $pageController('search.php' など)を
 * 設定してから読み込む。catalog_id で台帳を特定し、
 * catalogs/<working_dir>/<pageController> を埋め込んで $embedHtml に格納する。
 *
 * 埋め込まれる台帳側コントローラには以下の変数が渡る。
 *   $currentCatalogId   : 台帳ID
 *   $currentCatalogName : 台帳名
 *   $currentPage        : 親ページ名('search' | 'list' | 'register')
 *   $pageParams         : page_parameter で宣言されたクエリ値
 *   $embedUrl           : 自ページのURL(catalog_id 付き)
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : dashboard_logic.php から分離
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/menu_loader.php';

$currentPage = basename($pageController, '.php');
$currentCatalogId = isset($_GET['catalog_id']) ? (int)$_GET['catalog_id'] : 0;
$currentCatalogName = '';
$pageTitle = $currentPage;
$embedHtml = '<div class="callout callout-warning"><p>指定された機能が見つかりません。</p></div>';

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
        if ($function['page_controller'] === $pageController) {
            $currentFunction = $function;
            break;
        }
    }
}

if ($currentCatalog !== null && $currentFunction !== null) {
    $currentCatalogName = (string)$currentCatalog['catalog_name'];
    $pageTitle = $currentCatalogName . '（' . $currentFunction['function_name'] . '）';

    $workingDir = ltrim($currentCatalog['working_dir'], '/\\');
    $controllerPath = __DIR__ . '/../../' . $workingDir . '/' . $pageController;
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

        $embedUrl = cmdb_page_url($currentPage, $currentCatalogId);

        ob_start();
        try {
            require $real;
        } catch (\Exception $e) {
            echo '<div class="callout callout-danger"><p>画面の読み込みに失敗しました。</p></div>';
        }
        $embedHtml = ob_get_clean();

        cmdb_perf_mark('embed');
    }
}

require_once __DIR__ . '/../views/catalog_page_view.php';
