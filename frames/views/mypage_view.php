<?php
/**
 * ユーザー情報の変更画面
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
  <title>mcafeCMDB プロフィール変更</title>
  <link rel="shortcut icon" href="favicon.ico">
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="asset/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="asset/dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="asset/dist/css/skins/_all-skins.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <a href="dashboard.php" class="logo">
      <span class="logo-mini"><b>m</b>C</span>
      <span class="logo-lg"><b>mcafe</b>CMDB</span>
    </a>
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="hidden-xs"><?php echo htmlspecialchars($user['nick_name'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-footer">
                <div class="pull-left">
                  <a href="mypage.php" class="btn btn-default btn-flat">プロフィール</a>
                </div>
                <div class="pull-right">
                  <a href="logout.php" class="btn btn-default btn-flat">ログアウト</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>プロフィール変更</h1>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">ユーザー情報</h3>
            </div>
            <form action="" method="post" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="nick_name" class="col-sm-3 control-label">ニックネーム</label>
                  <div class="col-sm-9">
                    <input type="text" id="nick_name" name="nick_name" class="form-control" value="<?php echo htmlspecialchars($user['nick_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="passwd" class="col-sm-3 control-label">パスワード</label>
                  <div class="col-sm-9">
                    <input type="password" id="passwd" name="passwd" class="form-control" placeholder="新しいパスワード" required>
                  </div>
                </div>
                <?php if ($error !== '') { ?>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9">
                    <p class="text-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                </div>
                <?php } ?>
              </div>
              <div class="box-footer">
                <a href="dashboard.php" class="btn btn-default">戻る</a>
                <button type="submit" class="btn btn-primary pull-right">変更</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
<?php require_once __DIR__ . '/footer.php'; ?>
</div>
<script src="asset/bower_components/jquery/dist/jquery.min.js"></script>
<script src="asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="asset/dist/js/adminlte.min.js"></script>
</body>
</html>
