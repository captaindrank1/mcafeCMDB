<?php
/**
 * アカウント管理台帳：登録／編集のロジック部
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/10 1.0.0 鈴木(ゆ)  : 新規作成
 * - 2026/09/15 1.1.0 鈴木(ゆ)  : MUA選択肢を parameter_name='メーラー' から取得
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

require_once __DIR__ . '/../../../frames/logic/db_connection.php';

$regError = '';
$isEdit = false;
$primaryAccount = '';
$userName = '';
$userCategory = '';
$remarks = '';
$deletedFlag = 0;
$pk = '';
$serviceRows = array(array('service_name' => '', 'id' => '', 'passwd' => '', 'mail_address' => ''));
$muaRows = array(array('mua' => ''));
$categories = array();
$serviceOptions = array();
$muaOptions = array();

$action = isset($pageParams['action']) ? (string)$pageParams['action'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
}

if ($action === 'edit') {
    $isEdit = true;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pk = isset($_POST['pk']) ? trim((string)$_POST['pk']) : '';
    } else {
        $pk = isset($pageParams['pk']) ? trim((string)$pageParams['pk']) : '';
    }
}

/* 選択肢・台帳名を取得（表示・バリデーション共通） */
$catalogName = '';
try {
    $db = cmdb_db();

    $sql = <<<SQL
SELECT
    catalog_name
FROM
    CMDB_M_CATALOGS
WHERE
    catalog_id = ?
SQL;

    $catalogRow = $db->ExecuteSingle($sql, array((int)$currentCatalogId));

    if ($catalogRow === null) {
        $regError = '台帳情報が見つかりません。';
    } else {
        $catalogName = (string)$catalogRow['catalog_name'];

        $sql = <<<SQL
SELECT
    value
FROM
    CMDB_M_SYSPARAM
WHERE
    catalog_name = ?
    AND parameter_name = ?
ORDER BY
    display_order
SQL;

        $rows = $db->ExecuteQuery($sql, array($catalogName, '区分'));
        foreach ($rows as $row) {
            $categories[] = (string)$row['value'];
        }
        $rows = $db->ExecuteQuery($sql, array($catalogName, 'サービス'));
        foreach ($rows as $row) {
            $serviceOptions[] = (string)$row['value'];
        }
        $rows = $db->ExecuteQuery($sql, array($catalogName, 'メーラー'));
        foreach ($rows as $row) {
            $muaOptions[] = (string)$row['value'];
        }
    }
} catch (\Exception $e) {
    $regError = 'マスタ情報の取得に失敗しました。';
}

/* 編集時の表示用データ読み込み（GET のみ） */
if ($regError === '' && $isEdit && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($pk === '') {
        $regError = '編集対象が指定されていません。';
    } else {
        try {
            $db = cmdb_db();

            $sql = <<<SQL
SELECT
    primary_account,
    user_name,
    user_category,
    remarks,
    deleted_flag
FROM
    CMDB_CAT_PRIMARY_ACCOUNTS
WHERE
    primary_account = ?
SQL;

            $primary = $db->ExecuteSingle($sql, array($pk));

            if ($primary === null) {
                $regError = '編集対象が見つかりません。';
            } else {
                $primaryAccount = (string)$primary['primary_account'];
                $userName = (string)$primary['user_name'];
                $userCategory = (string)$primary['user_category'];
                $remarks = (string)$primary['remarks'];
                $deletedFlag = (int)$primary['deleted_flag'];

                $sql = <<<SQL
SELECT
    service_name,
    id,
    passwd,
    mail_address
FROM
    CMDB_CAT_ACCOUNT_LIST
WHERE
    primary_account = ?
    AND deleted_flag = 0
ORDER BY
    service_name
SQL;

                $serviceRows = $db->ExecuteQuery($sql, array($pk));
                if (count($serviceRows) === 0) {
                    $serviceRows = array(array('service_name' => '', 'id' => '', 'passwd' => '', 'mail_address' => ''));
                }

                $sql = <<<SQL
SELECT
    mua
FROM
    CMDB_CAT_USE_MUA
WHERE
    primary_account = ?
    AND deleted_flag = 0
ORDER BY
    mua
SQL;

                $muaRows = $db->ExecuteQuery($sql, array($pk));
                if (count($muaRows) === 0) {
                    $muaRows = array(array('mua' => ''));
                }
            }
        } catch (\Exception $e) {
            $regError = 'データの取得に失敗しました。';
        }
    }
}

/* POST 処理 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $regError === '') {
    $userName = isset($_POST['user_name']) ? trim((string)$_POST['user_name']) : '';
    $userCategory = isset($_POST['user_category']) ? trim((string)$_POST['user_category']) : '';
    $remarks = isset($_POST['remarks']) ? trim((string)$_POST['remarks']) : '';
    $deletedFlag = (isset($_POST['deleted_flag']) && $_POST['deleted_flag'] === '1') ? 1 : 0;

    if ($isEdit) {
        $primaryAccount = $pk;
    } else {
        $primaryAccount = isset($_POST['primary_account']) ? trim((string)$_POST['primary_account']) : '';
    }

    /* サービス行の取得 */
    $serviceRows = array();
    $postedServiceNames = isset($_POST['service_name']) ? (array)$_POST['service_name'] : array();
    $postedServiceIds = isset($_POST['service_id']) ? (array)$_POST['service_id'] : array();
    $postedServicePasswds = isset($_POST['service_passwd']) ? (array)$_POST['service_passwd'] : array();
    $postedServiceMails = isset($_POST['service_mail']) ? (array)$_POST['service_mail'] : array();
    $serviceCount = count($postedServiceNames);
    for ($i = 0; $i < $serviceCount; $i++) {
        $serviceRows[] = array(
            'service_name' => isset($postedServiceNames[$i]) ? trim((string)$postedServiceNames[$i]) : '',
            'id' => isset($postedServiceIds[$i]) ? trim((string)$postedServiceIds[$i]) : '',
            'passwd' => isset($postedServicePasswds[$i]) ? trim((string)$postedServicePasswds[$i]) : '',
            'mail_address' => isset($postedServiceMails[$i]) ? trim((string)$postedServiceMails[$i]) : '',
        );
    }
    if (count($serviceRows) === 0) {
        $serviceRows = array(array('service_name' => '', 'id' => '', 'passwd' => '', 'mail_address' => ''));
    }

    /* MUA 行の取得 */
    $muaRows = array();
    $postedMuas = isset($_POST['mua']) ? (array)$_POST['mua'] : array();
    foreach ($postedMuas as $m) {
        $muaRows[] = array('mua' => trim((string)$m));
    }
    if (count($muaRows) === 0) {
        $muaRows = array(array('mua' => ''));
    }

    /* バリデーション */
    if ($primaryAccount === '') {
        $regError = 'プライマリアカウントは必須です。';
    } elseif (mb_strlen($primaryAccount, 'UTF-8') > 24) {
        $regError = 'プライマリアカウントは24文字以内で入力してください。';
    } elseif ($userName === '') {
        $regError = '氏名は必須です。';
    } elseif (mb_strlen($userName, 'UTF-8') > 24) {
        $regError = '氏名は24文字以内で入力してください。';
    } elseif ($userCategory === '') {
        $regError = '区分は必須です。';
    } elseif (mb_strlen($userCategory, 'UTF-8') > 48) {
        $regError = '区分は48文字以内で入力してください。';
    } elseif (!in_array($userCategory, $categories, true)) {
        $regError = '区分の値が不正です。';
    } elseif (mb_strlen($remarks, 'UTF-8') > 1024) {
        $regError = '備考は1024文字以内で入力してください。';
    }

    /* サービス行バリデーション */
    $seenServices = array();
    if ($regError === '') {
        foreach ($serviceRows as $row) {
            if ($row['service_name'] === '' && $row['id'] === '' && $row['passwd'] === '' && $row['mail_address'] === '') {
                continue;
            }
            if ($row['service_name'] === '') {
                $regError = 'サービス名を選択してください。';
                break;
            }
            if (!in_array($row['service_name'], $serviceOptions, true)) {
                $regError = 'サービス名の値が不正です。';
                break;
            }
            if (mb_strlen($row['service_name'], 'UTF-8') > 48) {
                $regError = 'サービス名は48文字以内で入力してください。';
                break;
            }
            if (mb_strlen($row['id'], 'UTF-8') > 32) {
                $regError = 'サービスIDは32文字以内で入力してください。';
                break;
            }
            if (mb_strlen($row['passwd'], 'UTF-8') > 48) {
                $regError = 'パスワードは48文字以内で入力してください。';
                break;
            }
            if (mb_strlen($row['mail_address'], 'UTF-8') > 128) {
                $regError = 'メールアドレスは128文字以内で入力してください。';
                break;
            }
            if (isset($seenServices[$row['service_name']])) {
                $regError = '同じサービス名が複数指定されています。';
                break;
            }
            $seenServices[$row['service_name']] = true;
        }
    }

    /* MUA 行バリデーション */
    $seenMuas = array();
    if ($regError === '') {
        foreach ($muaRows as $row) {
            if ($row['mua'] === '') {
                continue;
            }
            if (!in_array($row['mua'], $muaOptions, true)) {
                $regError = 'MUAの値が不正です。';
                break;
            }
            if (mb_strlen($row['mua'], 'UTF-8') > 48) {
                $regError = 'MUAは48文字以内で入力してください。';
                break;
            }
            if (isset($seenMuas[$row['mua']])) {
                $regError = '同じMUAが複数指定されています。';
                break;
            }
            $seenMuas[$row['mua']] = true;
        }
    }

    /* DB 登録・更新 */
    if ($regError === '') {
        try {
            $db = cmdb_db();

            if ($isEdit) {
                /* 対象存在確認 */
                $sql = <<<SQL
SELECT
    COUNT(*)
FROM
    CMDB_CAT_PRIMARY_ACCOUNTS
WHERE
    primary_account = ?
SQL;
                $exists = $db->ExecuteScalar($sql, array($pk));
                if ((int)$exists === 0) {
                    $regError = '編集対象が見つかりません。';
                } else {
                    $db->BeginTransaction();
                    try {
                        $sql = <<<SQL
UPDATE
    CMDB_CAT_PRIMARY_ACCOUNTS
SET
    user_name = ?,
    user_category = ?,
    remarks = ?,
    deleted_flag = ?
WHERE
    primary_account = ?
SQL;
                        $db->ExecuteNonQuery($sql, array($userName, $userCategory, $remarks, $deletedFlag, $pk));

                        /* 既存サービスキー */
                        $sql = <<<SQL
SELECT
    service_name
FROM
    CMDB_CAT_ACCOUNT_LIST
WHERE
    primary_account = ?
SQL;
                        $existingServices = $db->ExecuteQuery($sql, array($pk));
                        $existingServiceKeys = array();
                        foreach ($existingServices as $row) {
                            $existingServiceKeys[$row['service_name']] = true;
                        }

                        $sqlInsert = <<<SQL
INSERT INTO
    CMDB_CAT_ACCOUNT_LIST
    (primary_account, service_name, id, passwd, mail_address, deleted_flag)
VALUES
    (?, ?, ?, ?, ?, 0)
SQL;
                        $sqlUpdate = <<<SQL
UPDATE
    CMDB_CAT_ACCOUNT_LIST
SET
    id = ?,
    passwd = ?,
    mail_address = ?,
    deleted_flag = 0
WHERE
    primary_account = ?
    AND service_name = ?
SQL;
                        $sqlDelete = <<<SQL
UPDATE
    CMDB_CAT_ACCOUNT_LIST
SET
    deleted_flag = 1
WHERE
    primary_account = ?
    AND service_name = ?
SQL;

                        $postedServiceKeys = array();
                        foreach ($serviceRows as $row) {
                            if ($row['service_name'] === '' && $row['id'] === '' && $row['passwd'] === '' && $row['mail_address'] === '') {
                                continue;
                            }
                            $postedServiceKeys[$row['service_name']] = true;
                            $id = ($row['id'] === '') ? null : $row['id'];
                            $passwd = ($row['passwd'] === '') ? null : $row['passwd'];
                            $mail = ($row['mail_address'] === '') ? null : $row['mail_address'];
                            if (isset($existingServiceKeys[$row['service_name']])) {
                                $db->ExecuteNonQuery($sqlUpdate, array($id, $passwd, $mail, $pk, $row['service_name']));
                            } else {
                                $db->ExecuteNonQuery($sqlInsert, array($pk, $row['service_name'], $id, $passwd, $mail));
                            }
                        }
                        foreach ($existingServiceKeys as $serviceName => $unused) {
                            if (!isset($postedServiceKeys[$serviceName])) {
                                $db->ExecuteNonQuery($sqlDelete, array($pk, $serviceName));
                            }
                        }

                        /* 既存MUAキー */
                        $sql = <<<SQL
SELECT
    mua
FROM
    CMDB_CAT_USE_MUA
WHERE
    primary_account = ?
SQL;
                        $existingMuas = $db->ExecuteQuery($sql, array($pk));
                        $existingMuaKeys = array();
                        foreach ($existingMuas as $row) {
                            $existingMuaKeys[$row['mua']] = true;
                        }

                        $sqlInsertMua = <<<SQL
INSERT INTO
    CMDB_CAT_USE_MUA
    (primary_account, mua, deleted_flag)
VALUES
    (?, ?, 0)
SQL;
                        $sqlUpdateMua = <<<SQL
UPDATE
    CMDB_CAT_USE_MUA
SET
    deleted_flag = 0
WHERE
    primary_account = ?
    AND mua = ?
SQL;
                        $sqlDeleteMua = <<<SQL
UPDATE
    CMDB_CAT_USE_MUA
SET
    deleted_flag = 1
WHERE
    primary_account = ?
    AND mua = ?
SQL;

                        $postedMuaKeys = array();
                        foreach ($muaRows as $row) {
                            if ($row['mua'] === '') {
                                continue;
                            }
                            $postedMuaKeys[$row['mua']] = true;
                            if (isset($existingMuaKeys[$row['mua']])) {
                                $db->ExecuteNonQuery($sqlUpdateMua, array($pk, $row['mua']));
                            } else {
                                $db->ExecuteNonQuery($sqlInsertMua, array($pk, $row['mua']));
                            }
                        }
                        foreach ($existingMuaKeys as $mua => $unused) {
                            if (!isset($postedMuaKeys[$mua])) {
                                $db->ExecuteNonQuery($sqlDeleteMua, array($pk, $mua));
                            }
                        }

                        $db->Commit();

                        header('Location: ' . $embedBaseUrl . 'list.php');
                        exit;
                    } catch (\Exception $e) {
                        $db->Rollback();
                        $regError = '更新に失敗しました。';
                    }
                }
            } else {
                /* 新規登録 */
                $sql = <<<SQL
SELECT
    COUNT(*)
FROM
    CMDB_CAT_PRIMARY_ACCOUNTS
WHERE
    primary_account = ?
SQL;
                $exists = $db->ExecuteScalar($sql, array($primaryAccount));
                if ((int)$exists > 0) {
                    $regError = '同じプライマリアカウントがすでに登録されています。';
                } else {
                    $db->BeginTransaction();
                    try {
                        $sql = <<<SQL
INSERT INTO
    CMDB_CAT_PRIMARY_ACCOUNTS
    (primary_account, user_name, user_category, remarks, deleted_flag)
VALUES
    (?, ?, ?, ?, ?)
SQL;
                        $db->ExecuteNonQuery($sql, array($primaryAccount, $userName, $userCategory, $remarks, $deletedFlag));

                        $sqlInsertService = <<<SQL
INSERT INTO
    CMDB_CAT_ACCOUNT_LIST
    (primary_account, service_name, id, passwd, mail_address, deleted_flag)
VALUES
    (?, ?, ?, ?, ?, 0)
SQL;
                        foreach ($serviceRows as $row) {
                            if ($row['service_name'] === '' && $row['id'] === '' && $row['passwd'] === '' && $row['mail_address'] === '') {
                                continue;
                            }
                            $id = ($row['id'] === '') ? null : $row['id'];
                            $passwd = ($row['passwd'] === '') ? null : $row['passwd'];
                            $mail = ($row['mail_address'] === '') ? null : $row['mail_address'];
                            $db->ExecuteNonQuery($sqlInsertService, array($primaryAccount, $row['service_name'], $id, $passwd, $mail));
                        }

                        $sqlInsertMua = <<<SQL
INSERT INTO
    CMDB_CAT_USE_MUA
    (primary_account, mua, deleted_flag)
VALUES
    (?, ?, 0)
SQL;
                        foreach ($muaRows as $row) {
                            if ($row['mua'] === '') {
                                continue;
                            }
                            $db->ExecuteNonQuery($sqlInsertMua, array($primaryAccount, $row['mua']));
                        }

                        $db->Commit();

                        header('Location: ' . $embedBaseUrl . 'list.php');
                        exit;
                    } catch (\Exception $e) {
                        $db->Rollback();
                        $regError = '登録に失敗しました。';
                    }
                }
            }
        } catch (\Exception $e) {
            $regError = $isEdit ? '更新に失敗しました。' : '登録に失敗しました。';
        }
    }
}

require_once __DIR__ . '/../views/register_view.php';
