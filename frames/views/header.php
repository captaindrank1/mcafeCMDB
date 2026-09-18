<?php
/**
 * 共通ページヘッダー(ロゴ・ユーザーメニュー)
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : dashboard_view.php から分離
 *
 * @category  View
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */
?>
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
