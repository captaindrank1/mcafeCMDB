<?php
/**
 * 台帳：条件検索（親ページ）のロジック部
 *
 * PHP version 5.4.16
 *
 * catalog_id で指定された台帳の search.php を catalog_loader 経由で埋め込む。
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

$pageController = 'search.php';

require_once __DIR__ . '/catalog_loader.php';
