<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">メールアドレス台帳（一覧）</h3>
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
          <th>氏名</th>
          <th>メールアドレス</th>
          <th>携帯メールアドレス</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($mailRows as $row) { ?>
        <tr>
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
