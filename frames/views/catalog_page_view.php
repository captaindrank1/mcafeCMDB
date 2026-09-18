<?php
/**
 * 台帳機能ページ(search / list / register)の共通画面
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : 新規作成
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
  <title>mcafeCMDB <?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="asset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="asset/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="asset/dist/css/skins/_all-skins.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<?php require __DIR__ . '/header.php'; ?>
<?php require __DIR__ . '/sidebar.php'; ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
    </section>
    <section class="content">
      <?php echo $embedHtml; ?>
    </section>
  </div>
<?php require_once __DIR__ . '/footer.php'; ?>
</div>
<script src="asset/bower_components/jquery/dist/jquery.min.js"></script>
<script src="asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="asset/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="asset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="asset/dist/js/adminlte.min.js"></script>
</body>
</html>
