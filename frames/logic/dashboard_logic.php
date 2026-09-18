<?php
/**
 * ダッシュボードのロジック部
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/06 1.0.0 鈴木(ゆ)  : 新規作成
 * - 2026/09/18 2.0.0 鈴木(ゆ)  : 台帳機能の埋め込みを search/list/register 親ページへ移管
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/menu_loader.php';

$infoFile = __DIR__ . '/../../information.txt';
$information = file_exists($infoFile) ? file_get_contents($infoFile) : 'information.txt が見つかりません。';

$currentCatalogId = 0;
$currentPage = '';

require_once __DIR__ . '/../views/dashboard_view.php';
