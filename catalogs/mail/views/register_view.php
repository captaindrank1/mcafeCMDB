<?php
/**
 * メールアドレス台帳：新規登録画面
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
    <h3 class="box-title">メールアドレス台帳（<?php echo $isEdit ? '編集' : '新規登録'; ?>）</h3>
  </div>
  <form action="<?php echo htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8'); ?>" method="post" class="form-horizontal">
    <input type="hidden" name="action" value="<?php echo $isEdit ? 'edit' : 'new'; ?>">
    <?php if ($isEdit) { ?>
    <input type="hidden" name="pk" value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>">
    <?php } ?>
    <div class="box-body">
      <div class="form-group">
        <label for="user_full_name" class="col-sm-3 control-label">氏名</label>
        <div class="col-sm-6">
          <?php if ($isEdit) { ?>
          <p class="form-control-static"><?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?></p>
          <?php } else { ?>
          <input type="text" id="user_full_name" name="user_full_name" class="form-control" value="<?php echo htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8'); ?>" required>
          <?php } ?>
        </div>
      </div>
      <div class="form-group">
        <label for="mail_address" class="col-sm-3 control-label">メールアドレス</label>
        <div class="col-sm-6">
          <input type="text" id="mail_address" name="mail_address" class="form-control" value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
      </div>
      <div class="form-group">
        <label for="mobile_address" class="col-sm-3 control-label">携帯メールアドレス</label>
        <div class="col-sm-6">
          <input type="text" id="mobile_address" name="mobile_address" class="form-control" value="<?php echo htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8'); ?>">
        </div>
      </div>
      <?php if ($regError !== '') { ?>
      <div class="form-group">
        <div class="col-sm-offset-3 col-sm-6">
          <p class="text-danger"><?php echo htmlspecialchars($regError, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
      </div>
      <?php } ?>
    </div>
    <div class="box-footer">
      <a href="<?php echo htmlspecialchars($embedBaseUrl . 'list.php', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-default">一覧へ戻る</a>
      <button type="submit" class="btn btn-primary pull-right"><?php echo $isEdit ? '更新' : '登録'; ?></button>
    </div>
  </form>
</div>
