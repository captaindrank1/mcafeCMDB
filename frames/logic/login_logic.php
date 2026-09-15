<?php
/**
 * ログインのロジック部
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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logon_id = isset($_POST['logon_id']) ? $_POST['logon_id'] : '';
    $passwd = isset($_POST['passwd']) ? $_POST['passwd'] : '';

    if ($logon_id === '' || $passwd === '') {
        $error = 'ログオンIDとパスワードを入力してください。';
    } else {
        try {
            $db = cmdb_db();

            $hashed = hash('sha256', $passwd);
            $sql = <<<SQL
                SELECT user_id
                     , full_name
                     , nick_name
                     , logon_id
                     , status
                FROM CMDB_M_USER
                WHERE logon_id = ?
                  AND passwd = ?
                LIMIT 1
SQL;
            $user = $db->ExecuteSingle($sql, array($logon_id, $hashed));

            if ($user) {
                $_SESSION['current_user'] = array(
                    'user_id' => $user['user_id'],
                    'full_name' => $user['full_name'],
                    'nick_name' => $user['nick_name'],
                    'logon_id' => $user['logon_id'],
                );
                header('Location: dashboard.php');
                exit;
            } elseif ($user && (int)$user['status'] !== 1) {
                $error = 'アカウントが無効です。';
            } else {
                $error = 'ログオンIDまたはパスワードが違います。';
            }
        } catch (Exception $e) {
            $error = 'システムエラーが発生しました。';
        }
    }
}

require_once __DIR__ . '/../views/login_view.php';
