<?php
/**
 * ログインのロジック部
 *
 * PHP version 5.4.16
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

session_start();

require_once __DIR__ . '/../../commonLib/Fundamentals/Database/MySqliDb.php';
use Fundamentals\Database\MySqliDb;

require_once __DIR__ . '/global_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logon_id = isset($_POST['logon_id']) ? $_POST['logon_id'] : '';
    $passwd = isset($_POST['passwd']) ? $_POST['passwd'] : '';

    if ($logon_id === '' || $passwd === '') {
        $error = 'ログオンIDとパスワードを入力してください。';
    } else {
        try {
            $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
            $db->Open();

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

            $db->Close();

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
