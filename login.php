<?php
/**
 * ログイン画面とその処理
 *
 * PHP version 5.4.16
 *
 * @category  Application
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */

// プロジェクト直下の login.php から見た、controls/ への相対パス
require_once 'controls/login_logic.php';
?>
<!DOCTYPE html>
<html>
<head>
  <!-- プロジェクト直下から見た asset/ へのパスなので「../」が不要になりスッキリします -->
  <link rel="stylesheet" href="asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="asset/dist/css/AdminLTE.min.css">
</head>
<body class="hold-transition login-page">
<!-- （中略：AdminLTE 2 のログインフォーム HTML） -->
  <form action="" method="post">
    <!-- input項目など -->
  </form>
</body>
</html>
