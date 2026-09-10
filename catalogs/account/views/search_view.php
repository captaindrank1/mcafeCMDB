<?php
/**
 * アカウント管理台帳：条件検索画面
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
<!DOCTYPE html>
<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title"><?php echo htmlspecialchars($currentCatalogName, ENT_QUOTES, 'UTF-8'); ?>（条件検索）</h3>
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
          <th>プライマリアカウント</th>
          <th>氏名</th>
          <th>区分</th>
          <th>備考</th>
          <th>サービス</th>
          <th>MUA</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($searchResults as $result) { ?>
        <tr>
          <td>
            <a href="dashboard.php?catalog_id=<?php echo (int)$currentCatalogId; ?>&amp;func=register.php&amp;action=edit&amp;pk=<?php echo rawurlencode($result['primary']['primary_account']); ?>" class="btn btn-default btn-xs">編集</a>
          </td>
          <td><?php echo htmlspecialchars($result['primary']['primary_account'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($result['primary']['user_name'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars($result['primary']['user_category'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo htmlspecialchars((string)$result['primary']['remarks'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td>
            <?php foreach ($result['services'] as $s) { ?>
            <div>
              <?php echo htmlspecialchars($s['service_name'], ENT_QUOTES, 'UTF-8'); ?> / ID:<?php echo htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?> / パスワード:<?php echo htmlspecialchars($s['passwd'], ENT_QUOTES, 'UTF-8'); ?> / メール:<?php echo htmlspecialchars($s['mail_address'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php } ?>
          </td>
          <td>
            <?php foreach ($result['muas'] as $m) { ?>
            <div>
              <?php echo htmlspecialchars($m['mua'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php } ?>
  </div>
</div>
<?php } ?>
