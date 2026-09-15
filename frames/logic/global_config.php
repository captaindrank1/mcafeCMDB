<?php
/**
 * 共通定数定義
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

/*
 * データベース関連定数
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'mcafeCMDB');
define('DB_USER', 'mcafeCMDB_admin');
define('DB_PASS', 'maruyama10');

/*
 * サイドメニュー(台帳・機能一覧)のセッションキャッシュ有効期間(秒)
 */
define('MENU_CACHE_TTL', 300);
