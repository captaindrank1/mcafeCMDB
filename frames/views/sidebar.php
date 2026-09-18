<?php
/**
 * 共通サイドバー(台帳・機能メニュー)
 *
 * PHP version 5.4.16
 *
 * $catalogs / $catalogFunctions / $currentCatalogId / $currentPage を参照する。
 *
 * 【改訂履歴】
 * - 2026/09/18 1.0.0 鈴木(ゆ)  : dashboard_view.php から分離、リンク先を親ページに変更
 *
 * @category  View
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */
?>
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
              <?php
              $functionPage = basename($function['page_controller'], '.php');
              $extraParams = array();
              if (isset($function['page_parameter']) && $function['page_parameter'] !== '') {
                  foreach (explode('&', $function['page_parameter']) as $pair) {
                      $kv = explode('=', $pair, 2);
                      if (count($kv) === 2 && trim($kv[0]) === 'action' && trim($kv[1]) === '%s') {
                          $extraParams['action'] = 'new';
                          break;
                      }
                  }
              }
              ?>
              <li<?php if ((int)$catalog['catalog_id'] === $currentCatalogId && $functionPage === $currentPage) { echo ' class="active"'; } ?>>
                <a href="<?php echo htmlspecialchars(cmdb_page_url($functionPage, $catalog['catalog_id'], $extraParams), ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-circle-o"></i> <?php echo htmlspecialchars($function['function_name'], ENT_QUOTES, 'UTF-8'); ?></a>
              </li>
              <?php } ?>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
      </ul>
    </section>
  </aside>
