<?php
/**
 * ユーザー情報の変更のロジック部
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

require_once __DIR__ . '/db_connection.php';

if (!isset($_SESSION['current_user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['current_user'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick_name = isset($_POST['nick_name']) ? trim($_POST['nick_name']) : '';
    $passwd = isset($_POST['passwd']) ? $_POST['passwd'] : '';

    if ($nick_name === '') {
        $error = 'ニックネームを入力してください。';
    } elseif ($passwd === '') {
        $error = 'パスワードは必ず設定してください。';
    } else {
        try {
            $db = cmdb_db();

            $hashed = hash('sha256', $passwd);
            $sql = <<<SQL
                UPDATE CMDB_M_USER
                SET
                       nick_name = ?
                     , passwd = ?
                WHERE user_id = ?
SQL;

            $db->ExecuteNonQuery($sql, array($nick_name, $hashed, $user['user_id']));

            $_SESSION['current_user']['nick_name'] = $nick_name;

            header('Location: dashboard.php');
            exit;
        } catch (\Exception $e) {
            $error = 'システムエラーが発生しました。';
        }
    }
}

require_once __DIR__ . '/../views/mypage_view.php';
