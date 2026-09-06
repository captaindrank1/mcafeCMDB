<?php
/**
 * メールアドレス台帳：条件検索画面
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
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">メールアドレス台帳（条件検索）</h3>
  </div>
  <form action="" method="get" class="form-horizontal">
    <input type="hidden" name="catalog_id" value="<?php echo (int)$currentCatalogId; ?>">
    <input type="hidden" name="func" value="<?php echo htmlspecialchars($currentFunc, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="box-body">
      <div class="form-group">
        <label for="keyword" class="col-sm-2 control-label">キーワード</label>
        <div class="col-sm-6">
          <input type="text" id="keyword" name="keyword" class="form-control" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
    </div>
    <div class="box-footer">
      <button type="submit" class="btn btn-primary pull-right">検索</button>
    </div>
  </form>
</div>

<?php if ($searched) { ?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title">検索結果</h3>
  </div>
  <div class="box-body">
    <?php if ($searchError !== '') { ?>
    <div class="callout callout-danger">
      <p><?php echo htmlspecialchars($searchError, ENT_QUOTES, 'UTF-8'); ?></p>
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
        <?php foreach ($searchRows as $row) { ?>
        <tr>
          <td>
            <a href="dashboard.php?catalog_id=<?php echo (int)$currentCatalogId; ?>&amp;func=register.php&amp;action=edit&amp;pk=<?php echo rawurlencode($row['user_full_name']); ?>" class="btn btn-default btn-xs">編集</a>
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
<?php } ?>
