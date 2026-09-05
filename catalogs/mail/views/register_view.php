<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">メールアドレス台帳（新規登録）</h3>
  </div>
  <form action="<?php echo htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8'); ?>" method="post" class="form-horizontal">
    <div class="box-body">
      <div class="form-group">
        <label for="user_full_name" class="col-sm-3 control-label">氏名</label>
        <div class="col-sm-6">
          <input type="text" id="user_full_name" name="user_full_name" class="form-control" required>
        </div>
      </div>
      <div class="form-group">
        <label for="mail_address" class="col-sm-3 control-label">メールアドレス</label>
        <div class="col-sm-6">
          <input type="text" id="mail_address" name="mail_address" class="form-control" required>
        </div>
      </div>
      <div class="form-group">
        <label for="mobile_address" class="col-sm-3 control-label">携帯メールアドレス</label>
        <div class="col-sm-6">
          <input type="text" id="mobile_address" name="mobile_address" class="form-control">
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
      <button type="submit" class="btn btn-primary pull-right">登録</button>
    </div>
  </form>
</div>
