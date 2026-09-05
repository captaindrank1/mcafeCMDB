<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>mcafeCMDB Dashboard</title>
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
  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li<?php if ($currentCatalogId === 0) { echo ' class="active"'; } ?>><a href="dashboard.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="header">CATALOGS</li>
        <?php foreach ($catalogs as $catalog) { ?>
        <li class="treeview<?php if ((int)$catalog['catalog_id'] === $currentCatalogId) { echo ' active'; } ?>">
          <a href="#">
            <i class="fa fa-folder"></i> <span><?php echo htmlspecialchars($catalog['catalog_name'], ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <?php if (isset($catalogFunctions[$catalog['catalog_id']])) { ?>
              <?php foreach ($catalogFunctions[$catalog['catalog_id']] as $function) { ?>
              <li<?php if ((int)$catalog['catalog_id'] === $currentCatalogId && $function['page_controller'] === $currentFunc) { echo ' class="active"'; } ?>>
                <a href="dashboard.php?catalog_id=<?php echo (int)$catalog['catalog_id']; ?>&func=<?php echo rawurlencode($function['page_controller']); ?>"><i class="fa fa-circle-o"></i> <?php echo htmlspecialchars($function['function_name'], ENT_QUOTES, 'UTF-8'); ?></a>
              </li>
              <?php } ?>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
      </ul>
    </section>
  </aside>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Dashboard</h1>
    </section>
    <section class="content">
      <?php if ($embedHtml !== null) { ?>
        <?php echo $embedHtml; ?>
      <?php } else { ?>
      <div class="callout callout-info">
        <p><?php echo nl2br(htmlspecialchars($information, ENT_QUOTES, 'UTF-8')); ?></p>
      </div>
      <?php } ?>
    </section>
  </div>
<?php require_once __DIR__ . '/footer.php'; ?>
</div>
<script src="asset/bower_components/jquery/dist/jquery.min.js"></script>
<script src="asset/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="asset/dist/js/adminlte.min.js"></script>
</body>
</html>
