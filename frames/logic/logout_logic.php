<?php
/**
 * ログアウトのロジック部
 *
 * PHP version 5.4.16
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

session_start();

$_SESSION = array();

$cookie = session_name();
if (isset($_COOKIE[$cookie])) {
    $params = session_get_cookie_params();
    setcookie(
        $cookie,
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();
header('Location: login.php');
exit;
