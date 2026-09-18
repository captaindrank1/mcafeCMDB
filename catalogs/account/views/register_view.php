<?php
/**
 * アカウント管理台帳：新規登録画面
 *
 * PHP version 5.4.16
 *
 * 【改訂履歴】
 * - 2026/09/10 1.0.0 鈴木(ゆ)  : 新規作成
 * - 2026/09/15 1.1.0 鈴木(ゆ)  : MUAの選択をチェックボックスに変更
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
    <h3 class="box-title"><?php echo htmlspecialchars($currentCatalogName, ENT_QUOTES, 'UTF-8'); ?>（<?php echo $isEdit ? '編集' : '新規登録'; ?>）</h3>
  </div>
  <form action="<?php echo htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8'); ?>" method="post" class="form-horizontal">
    <input type="hidden" name="action" value="<?php echo $isEdit ? 'edit' : 'new'; ?>">
    <?php if ($isEdit) { ?>
    <input type="hidden" name="pk" value="<?php echo htmlspecialchars($primaryAccount, ENT_QUOTES, 'UTF-8'); ?>">
    <?php } ?>
    <div class="box-body">
      <h4>基本情報</h4>
      <div class="form-group">
        <label for="primary_account" class="col-sm-3 control-label">プライマリアカウント</label>
        <div class="col-sm-6">
          <?php if ($isEdit) { ?>
          <p class="form-control-static"><?php echo htmlspecialchars($primaryAccount, ENT_QUOTES, 'UTF-8'); ?></p>
          <?php } else { ?>
          <input type="text" id="primary_account" name="primary_account" class="form-control" value="<?php echo htmlspecialchars($primaryAccount, ENT_QUOTES, 'UTF-8'); ?>" maxlength="24" required>
          <?php } ?>
        </div>
      </div>
      <div class="form-group">
        <label for="user_name" class="col-sm-3 control-label">氏名</label>
        <div class="col-sm-6">
          <input type="text" id="user_name" name="user_name" class="form-control" value="<?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>" maxlength="24" required>
        </div>
      </div>
      <div class="form-group">
        <label for="user_category" class="col-sm-3 control-label">区分</label>
        <div class="col-sm-6">
          <select id="user_category" name="user_category" class="form-control" required>
            <option value=""></option>
            <?php foreach ($categories as $opt) { ?>
            <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>"<?php if ($userCategory === $opt) { echo ' selected'; } ?>><?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label for="remarks" class="col-sm-3 control-label">備考</label>
        <div class="col-sm-6">
          <textarea id="remarks" name="remarks" class="form-control" rows="3" maxlength="1024"><?php echo htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-3 control-label">削除フラグ</label>
        <div class="col-sm-6">
          <label class="switch">
            <input type="checkbox" name="deleted_flag" value="1"<?php if ($deletedFlag === 1) { echo ' checked'; } ?>>
            <span class="slider"></span>
          </label>
          <span class="text-muted"> ON で論理削除</span>
        </div>
      </div>

      <h4>利用サービス</h4>
      <div class="form-group">
        <div class="col-sm-12">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th style="width:25%">サービス名</th>
                <th style="width:20%">ID</th>
                <th style="width:20%">パスワード</th>
                <th style="width:25%">メールアドレス</th>
                <th style="width:10%"></th>
              </tr>
            </thead>
            <tbody id="serviceRows">
              <?php foreach ($serviceRows as $s) { ?>
              <tr>
                <td>
                  <select name="service_name[]" class="form-control">
                    <option value=""></option>
                    <?php foreach ($serviceOptions as $opt) { ?>
                    <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>"<?php if ($s['service_name'] === $opt) { echo ' selected'; } ?>><?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php } ?>
                  </select>
                </td>
                <td><input type="text" name="service_id[]" class="form-control" value="<?php echo htmlspecialchars($s['id'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="32"></td>
                <td><input type="text" name="service_passwd[]" class="form-control" value="<?php echo htmlspecialchars($s['passwd'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="48"></td>
                <td><input type="text" name="service_mail[]" class="form-control" value="<?php echo htmlspecialchars($s['mail_address'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="128"></td>
                <td><button type="button" class="btn btn-danger btn-xs" onclick="removeRow(this)">削除</button></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <button type="button" class="btn btn-default btn-sm" onclick="addServiceRow()">サービスを追加</button>
        </div>
      </div>

      <h4>利用メーラー（MUA）</h4>
      <div class="form-group">
        <div class="col-sm-12">
          <?php
          $selectedMuas = array();
          foreach ($muaRows as $m) {
              if ($m['mua'] !== '') {
                  $selectedMuas[$m['mua']] = true;
              }
          }
          ?>
          <?php if (count($muaOptions) === 0) { ?>
          <p class="text-muted">選択可能な MUA が登録されていません。</p>
          <?php } else { ?>
          <?php foreach ($muaOptions as $opt) { ?>
          <label class="checkbox-inline">
            <input type="checkbox" name="mua[]" value="<?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>"<?php if (isset($selectedMuas[$opt])) { echo ' checked'; } ?>> <?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>
          </label>
          <?php } ?>
          <?php } ?>
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
      <a href="<?php echo htmlspecialchars(cmdb_page_url('list', $currentCatalogId), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-default">一覧へ戻る</a>
      <button type="submit" class="btn btn-primary pull-right"><?php echo $isEdit ? '更新' : '登録'; ?></button>
    </div>
  </form>
</div>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 46px;
  height: 24px;
}
.switch input {
  display: none;
}
.switch .slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .3s;
  transition: .3s;
  border-radius: 24px;
}
.switch .slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: #fff;
  -webkit-transition: .3s;
  transition: .3s;
  border-radius: 50%;
}
.switch input:checked + .slider {
  background-color: #3c8dbc;
}
.switch input:checked + .slider:before {
  -webkit-transform: translateX(22px);
  -ms-transform: translateX(22px);
  transform: translateX(22px);
}
</style>

<script>
var serviceOptions = <?php echo json_encode($serviceOptions); ?>;

function removeRow(btn) {
    var node = btn;
    while (node && node.tagName !== 'TR') {
        node = node.parentNode;
    }
    if (node && node.parentNode) {
        node.parentNode.removeChild(node);
    }
}

function buildSelect(name, options) {
    var sel = document.createElement('select');
    sel.name = name;
    sel.className = 'form-control';
    var o = document.createElement('option');
    o.value = '';
    o.text = '';
    sel.appendChild(o);
    for (var i = 0; i < options.length; i++) {
        o = document.createElement('option');
        o.value = options[i];
        o.text = options[i];
        sel.appendChild(o);
    }
    return sel;
}

function buildInput(name, value) {
    var input = document.createElement('input');
    input.type = 'text';
    input.name = name;
    input.className = 'form-control';
    if (value) {
        input.value = value;
    }
    return input;
}

function buildRemoveButton() {
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'btn btn-danger btn-xs';
    btn.innerHTML = '削除';
    btn.onclick = function () { removeRow(btn); };
    return btn;
}

function appendRow(tbodyId, cells) {
    var tbody = document.getElementById(tbodyId);
    var tr = document.createElement('tr');
    for (var i = 0; i < cells.length; i++) {
        var td = document.createElement('td');
        td.appendChild(cells[i]);
        tr.appendChild(td);
    }
    tbody.appendChild(tr);
}

function addServiceRow() {
    appendRow('serviceRows', [
        buildSelect('service_name[]', serviceOptions),
        buildInput('service_id[]'),
        buildInput('service_passwd[]'),
        buildInput('service_mail[]'),
        buildRemoveButton()
    ]);
}
</script>
