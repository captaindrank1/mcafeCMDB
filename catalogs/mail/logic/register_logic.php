<?php
/**
 * メールアドレス台帳：登録／編集のロジック部
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/06 1.0.0 鈴木(ゆ)  : 新規作成
 * - 2026/09/18 1.1.0 鈴木(ゆ)  : 登録後の遷移先を list.php 親ページに変更
 *
 * @category  Logic
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/../../../frames/logic/db_connection.php';

$regError = '';
$isEdit = false;
$fullName = '';
$pk = '';
$mail = '';
$mobile = '';

$action = isset($pageParams['action']) ? (string)$pageParams['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
}

if ($action === 'edit') {
    $pk = isset($pageParams['pk']) ? trim((string)$pageParams['pk']) : '';

    if ($pk === '') {
        $regError = '編集対象が指定されていません。';
    } else {
        try {
            $db = cmdb_db();
            $editRow = $db->ExecuteSingle(
                'SELECT user_full_name, mail_address, mobile_address FROM CMDB_CAT_MAIL WHERE user_full_name = ?',
                array($pk)
            );

            if ($editRow === null) {
                $regError = '編集対象が見つかりません。';
            } else {
                $isEdit = true;
                $fullName = (string)$editRow['user_full_name'];
                $pk = $fullName;
                $mail = (string)$editRow['mail_address'];
                $mobile = (string)$editRow['mobile_address'];
            }
        } catch (\Exception $e) {
            $regError = 'データの取得に失敗しました。';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = isset($_POST['mail_address']) ? trim($_POST['mail_address']) : '';
    $mobile = isset($_POST['mobile_address']) ? trim($_POST['mobile_address']) : '';

    if ($action === 'edit') {
        $pk = isset($_POST['pk']) ? trim($_POST['pk']) : '';
        $fullName = $pk;

        if ($pk === '' || $mail === '') {
            $regError = '氏名とメールアドレスは必須です。';
        } else {
            try {
                $db = cmdb_db();
                $db->ExecuteNonQuery(
                    'UPDATE CMDB_CAT_MAIL SET mail_address = ?, mobile_address = ? WHERE user_full_name = ?',
                    array($mail, $mobile === '' ? null : $mobile, $pk)
                );

                header('Location: ' . cmdb_page_url('list', $currentCatalogId));
                exit;
            } catch (\Exception $e) {
                $regError = '更新に失敗しました。';
            }
        }
    } else {
        $fullName = isset($_POST['user_full_name']) ? trim($_POST['user_full_name']) : '';
        $pk = $fullName;

        if ($fullName === '' || $mail === '') {
            $regError = '氏名とメールアドレスは必須です。';
        } else {
            try {
                $db = cmdb_db();

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
                }

                if ($regError === '') {
                    header('Location: ' . cmdb_page_url('list', $currentCatalogId));
                    exit;
                }
            } catch (\Exception $e) {
                $regError = '登録に失敗しました。';
            }
        }
    }
}

require_once __DIR__ . '/../views/register_view.php';
