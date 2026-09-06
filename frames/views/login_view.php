<?php
/**
 * ログイン画面
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/06 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  View
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>mcafeCMDB ログイン</title>
  <link rel="stylesheet" href="asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="asset/dist/css/AdminLTE.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>mcafe</b>CMDB</a>
  </div>
  <div class="login-box-body">
    <p class="login-box-msg">サインインしてください</p>
    <form action="" method="post">
      <div class="form-group has-feedback">
        <input type="text" name="logon_id" class="form-control" placeholder="ログオンID" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="passwd" class="form-control" placeholder="パスワード" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-4 col-xs-offset-8">
          <button type="submit" class="btn btn-primary btn-block btn-flat">ログイン</button>
        </div>
      </div>
      <?php if ($error !== '') { ?>
        <p class="text-danger" style="margin-top: 15px;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php } ?>
    </form>
  </div>
</div>
<script src="asset/bower_components/jquery/dist/jquery.min.js"></script>
<script src="asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
