<?php
/**
 * メールアドレス台帳：新規登録のロジック部
 *
 * PHP version 5.4.16
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/../../../frames/logic/global_config.php';
require_once __DIR__ . '/../../../commonLib/Fundamentals/Database/MySqliDb.php';
use Fundamentals\Database\MySqliDb;

$regError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = isset($_POST['user_full_name']) ? trim($_POST['user_full_name']) : '';
    $mail = isset($_POST['mail_address']) ? trim($_POST['mail_address']) : '';
    $mobile = isset($_POST['mobile_address']) ? trim($_POST['mobile_address']) : '';

    if ($fullName === '' || $mail === '') {
        $regError = '氏名とメールアドレスは必須です。';
    } else {
        try {
            $db = new MySqliDb(DB_HOST, DB_NAME, DB_USER, DB_PASS);
            $db->Open();

            $exists = $db->ExecuteScalar(
                'SELECT COUNT(*) FROM CMDB_CAT_MAIL WHERE user_full_name = ?',
                array($fullName)
            );

            if ((int)$exists > 0) {
                $regError = '同じ氏名がすでに登録されています。';
            } else {
                $db->ExecuteNonQuery(
                    'INSERT INTO CMDB_CAT_MAIL (user_full_name, mail_address, mobile_address) VALUES (?, ?, ?)',
                    array($fullName, $mail, $mobile === '' ? null : $mobile)
                );
                $db->Close();
                header('Location: ' . $embedBaseUrl . 'list.php');
                exit;
            }

            $db->Close();
        } catch (\Exception $e) {
            $regError = '登録に失敗しました。';
        }
    }
}

require_once __DIR__ . '/../views/register_view.php';
