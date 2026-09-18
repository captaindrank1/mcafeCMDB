<?php
/**
 * アカウント管理台帳：一覧表示画面
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/10 1.0.0 鈴木(ゆ)  : 新規作成
 * - 2026/09/15 1.1.0 鈴木(ゆ)  : 一覧を DataTable 化(ページング)、[新規登録]ボタンを追加
 * - 2026/09/18 1.2.0 鈴木(ゆ)  : リンク先を親ページ(search/list/register.php)に変更
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
    <div class="box-tools pull-right">
      <a href="<?php echo htmlspecialchars(cmdb_page_url('register', $currentCatalogId, array('action' => 'new')), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary btn-sm">新規登録</a>
    </div>
  </div>
  <div class="box-body">
    <?php if ($listError !== '') { ?>
    <div class="callout callout-danger">
      <p><?php echo htmlspecialchars($listError, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <?php } else { ?>
    <div class="table-responsive">
      <table id="accountTable" class="table table-bordered">
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
            <a href="<?php echo htmlspecialchars(cmdb_page_url('register', $currentCatalogId, array('action' => 'edit', 'pk' => $row['primary_account'])), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-default btn-xs">編集</a>
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
<script>
window.addEventListener('load', function () {
    if (typeof jQuery === 'undefined' || !jQuery.fn.dataTable) {
        return;
    }
    jQuery('#accountTable').DataTable({
        paging: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        ordering: true,
        order: [[1, 'asc']],
        columnDefs: [{ targets: 0, orderable: false, searchable: false }],
        autoWidth: false,
        language: {
            lengthMenu: '_MENU_ 件を表示',
            zeroRecords: '該当するデータはありません。',
            info: '_TOTAL_ 件中 _START_ ～ _END_ 件を表示',
            infoEmpty: '0 件',
            infoFiltered: '(全 _MAX_ 件から絞り込み)',
            search: '検索:',
            paginate: { first: '最初', last: '最後', next: '次', previous: '前' }
        }
    });
});
</script>
