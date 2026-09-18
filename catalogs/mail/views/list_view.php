<?php
/**
 * メールアドレス台帳：一覧表示画面
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/06 1.0.0 鈴木(ゆ)  : 新規作成
 * - 2026/09/18 1.1.0 鈴木(ゆ)  : リンク先を親ページ(search/list/register.php)に変更
 *
 * @category  View
 * @package   mcafeCMDB
 * @author    Yuji Suzuki
 * @copyright 2026 MARUYAMA COFFEE Co., Ltd.
 */
?>
<!DOCTYPE html>
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
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>操作</th>
          <th>氏名</th>
          <th>メールアドレス</th>
          <th>携帯メールアドレス</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mailRows as $row) { ?>
        <tr>
          <td>
            <a href="<?php echo htmlspecialchars(cmdb_page_url('register', $currentCatalogId, array('action' => 'edit', 'pk' => $row['user_full_name'])), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-default btn-xs">編集</a>
          </td>
          <td><?php echo htmlspecialchars($row['user_full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($row['mail_address'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars((string)$row['mobile_address'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php } ?>
  </div>
</div>
