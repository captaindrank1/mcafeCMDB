<?php
/**
 * アカウント管理台帳：一覧表示画面
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/10 1.0.0 鈴木(ゆ)  : 新規作成
 *
 * @category  View
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */
?>
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><?php echo htmlspecialchars($currentCatalogName, ENT_QUOTES, 'UTF-8'); ?>（一覧）</h3>
  </div>
  <div class="box-body">
    <?php if ($listError !== '') { ?>
    <div class="callout callout-danger">
      <p><?php echo htmlspecialchars($listError, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php } else { ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
          <th style="width:  1%; white-space: nowrap; text-align: center;">操作</th>
          <th style="width: 10%; white-space: nowrap; text-align: center;">プライマリアカウント</th>
          <th style="width: 10%; white-space: nowrap; text-align: center;">氏名</th>
          <th style="width: 10%; white-space: nowrap; text-align: center;">区分</th>
          <th style="white-space: nowrap; text-align: center;">備考</th>
          <th style="width: 15%; white-space: nowrap; text-align: center;">更新日時</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($accountRows as $row) { ?>
        <tr>
          <td>
            <a href="dashboard.php?catalog_id=<?php echo (int)$currentCatalogId; ?>&amp;func=register.php&amp;action=edit&amp;pk=<?php echo rawurlencode($row['primary_account']); ?>" class="btn btn-default btn-xs">編集</a>
          </td>
          <td><?php echo htmlspecialchars($row['primary_account'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($row['user_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($row['user_category'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars((string)$row['remarks'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars((string)$row['update_time'], ENT_QUOTES, 'UTF-8'); ?></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
    <?php } ?>
  </div>
</div>
