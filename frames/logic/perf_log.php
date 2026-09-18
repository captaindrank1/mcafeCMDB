<?php
/**
 * 処理時間計測ログ
 *
 * PHP version 5.4.16
 *
 * global_config.php の PERF_LOG_ENABLED / PERF_LOG_PATH で制御する。
 * OFF のときは判定のみで microtime() もファイル書き込みも行わない。
 * ON のときは計測値をメモリに溜め、シャットダウン時に1回だけ追記する。
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/global_config.php';

if (!defined('PERF_LOG_ENABLED')) {
    define('PERF_LOG_ENABLED', false);
}
if (!defined('PERF_LOG_PATH')) {
    define('PERF_LOG_PATH', __DIR__ . '/../../logs/perf.log');
}

/**
 * 計測ポイントを記録する
 *
 * @param string $label 計測ポイント名
 * @return void
 */
function cmdb_perf_mark($label)
{
    static $marks = null;
    static $start = null;

    if (!PERF_LOG_ENABLED) {
        return;
    }

    $now = microtime(true);

    if ($marks === null) {
        $marks = array();
        $start = isset($_SERVER['REQUEST_TIME_FLOAT']) ? (float)$_SERVER['REQUEST_TIME_FLOAT'] : $now;

        register_shutdown_function(function () use (&$marks, $start) {
            $end = microtime(true);
            $parts = array();
            $prev = $start;
            foreach ($marks as $mark) {
                $parts[] = sprintf('%s=%.1fms', $mark[0], ($mark[1] - $prev) * 1000);
                $prev = $mark[1];
            }
            $parts[] = sprintf('total=%.1fms', ($end - $start) * 1000);

            $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '-';
            $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '-';
            $line = sprintf(
                "%s\t%s\t%s\t%s\n",
                date('Y-m-d H:i:s'),
                $method,
                $uri,
                implode(' ', $parts)
            );

            @file_put_contents(PERF_LOG_PATH, $line, FILE_APPEND | LOCK_EX);
        });
    }

    $marks[] = array((string)$label, $now);
}
