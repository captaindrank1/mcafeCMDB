# mcafeCMDB

MARUYAMA COFFEE 社内向けの構成管理データベース(CMDB - Configuration Management Database)です。
「台帳(カタログ)」単位で情報を管理し、各台帳に対して「条件検索」「一覧表示」「登録／編集」の機能を提供します。

本ドキュメントはプログラムの保守・引き継ぎを目的としています。
テーブル作成用の DDL は別途提供されるため、ここでは扱いません。

## 動作環境

| 項目 | 内容 |
| --- | --- |
| Web サーバー | Apache HTTP Server(`.htaccess` を使用するため `AllowOverride All` が必要) |
| PHP | 5.4.16(**PHP 5.4 互換の構文のみ使用すること**。短縮配列構文 `[]` は使用可、`??` や型宣言などは使用不可) |
| DB | MySQL(`mysqli` 拡張を使用) |
| フロントエンド | AdminLTE 2 / Bootstrap 3 / jQuery / DataTables(`asset/` 配下、Bower で導入) |

フレームワークやパッケージマネージャ(Composer 等)は使用していません。ドキュメントルートにそのまま配置すれば動作します。

### 構文チェック

```sh
find . -name '*.php' -not -path './asset/*' -exec php -l {} \;
```

---

## 1. プログラム構造

### 1.1 全体構造の解説

#### ディレクトリ構成

```
/
├─ .htaccess                  Options -Indexes, DirectoryIndex login.php
├─ login.php                  ログイン
├─ logout.php                 ログアウト
├─ dashboard.php              ダッシュボード(トップ)
├─ mypage.php                 ユーザー情報(ニックネーム・パスワード)の変更
├─ search.php                 台帳:条件検索(親ページ)
├─ list.php                   台帳:一覧表示(親ページ)
├─ register.php               台帳:登録／編集(親ページ)
├─ information.txt            ダッシュボードに表示するお知らせ文
├─ favicon.ico
│
├─ frames/                    共通フレーム(直接アクセス禁止)
│   ├─ logic/                 各ページのロジック部
│   │   ├─ global_config.php  共通定数(DB 接続情報など)
│   │   ├─ db_connection.php  共有 DB 接続 cmdb_db()
│   │   ├─ perf_log.php       処理時間計測ログ cmdb_perf_mark()
│   │   ├─ menu_loader.php    認証確認 + サイドバーメニュー取得 + cmdb_page_url()
│   │   ├─ catalog_loader.php 台帳機能ページの共通ロジック(台帳側コントローラの埋め込み)
│   │   ├─ login_logic.php / logout_logic.php / mypage_logic.php / dashboard_logic.php
│   │   └─ search_logic.php / list_logic.php / register_logic.php
│   └─ views/                 各ページの画面部
│       ├─ header.php / sidebar.php / footer.php   共通パーツ
│       ├─ catalog_page_view.php                   台帳機能ページ共通レイアウト
│       └─ login_view.php / dashboard_view.php / mypage_view.php
│
├─ catalogs/                  台帳ごとの実装(直接アクセス禁止)
│   ├─ account/               アカウント管理台帳
│   └─ mail/                  メールアドレス台帳
│       ├─ search.php / list.php / register.php   台帳側コントローラ
│       ├─ logic/             *_logic.php
│       └─ views/             *_view.php
│
├─ commonLib/                 共通ライブラリ(直接アクセス禁止)
│   └─ Fundamentals/Database/ DbBase.php, MySqliDb.php, PdoDb.php
│
├─ asset/                     AdminLTE / Bootstrap / jQuery / DataTables 等(Git 管理外の部分あり)
└─ logs/                      perf.log 出力先(直接アクセス禁止、*.log は Git 管理外)
```

`frames/`、`catalogs/`、`commonLib/`、`logs/` には `Require all denied` の `.htaccess` を置いており、
ブラウザから直接アクセスできるのはドキュメントルート直下の `*.php` と `asset/` のみです。

#### ページの 3 層構成(Page Controller / Logic / View)

すべてのページは次の 3 ファイルで構成されます。

| 層 | 置き場所 | 役割 |
| --- | --- | --- |
| Page Controller | ドキュメントルート直下 `xxx.php` | `require_once 'frames/logic/xxx_logic.php';` の 1 行のみ |
| Logic | `frames/logic/xxx_logic.php` | セッション確認、DB アクセス、入力検証、リダイレクト。最後に View を `require` |
| View | `frames/views/xxx_view.php` | HTML 出力。Logic で用意した変数を `htmlspecialchars()` して表示するだけ |

台帳側(`catalogs/<台帳>/`)も同じ 3 層で構成されます(後述 2.2)。

#### リクエストの流れ(台帳機能ページの場合)

例: `list.php?catalog_id=1`

```
list.php
 └─ frames/logic/list_logic.php          $pageController = 'list.php';
     └─ frames/logic/catalog_loader.php
         ├─ frames/logic/menu_loader.php
         │    ├─ session_start()
         │    ├─ db_connection.php / perf_log.php を読み込み
         │    ├─ 未ログインなら login.php へリダイレクト
         │    └─ $user / $catalogs / $catalogFunctions を設定(セッションキャッシュあり)
         ├─ catalog_id から $currentCatalog を特定
         ├─ CMDB_M_CATALOG_FUNCTIONS から page_controller = 'list.php' の行を特定
         ├─ page_parameter に宣言されたクエリ値を $pageParams に格納
         ├─ catalogs/<working_dir>/list.php を ob_start() 内で require → $embedHtml
         └─ frames/views/catalog_page_view.php
              ├─ header.php / sidebar.php
              ├─ <section class="content"> に $embedHtml を出力
              └─ footer.php
```

親ページ(`search.php` / `list.php` / `register.php`)はレイアウト(ヘッダー・サイドバー・フッター)を担当し、
台帳固有の画面は `catalogs/` 配下のコントローラが出力した HTML 断片を埋め込む構造です。
そのため台帳側の View は `<html>` や `<body>` を出力せず、`<div class="box">...</div>` 等の断片のみを出力します。

`catalog_loader.php` は `realpath()` で解決したパスが `catalogs/` 配下であることを確認してから `require` します
(`working_dir` に不正な値が入ってもディレクトリトラバーサルにならないようにするため)。

#### 認証

- `login.php` で `CMDB_M_USER` を `logon_id` と `passwd`(SHA-256 ハッシュ)で照合し、成功したら `$_SESSION['current_user']` に格納して `dashboard.php` へ遷移します。
- ログインが必要なページは `menu_loader.php`(または `mypage_logic.php`)の先頭で `$_SESSION['current_user']` の有無を確認し、未ログインなら `login.php` へリダイレクトします。
- `logout.php` はセッションと Cookie を破棄して `login.php` へ戻ります。

#### DB アクセス

- `commonLib/Fundamentals/Database/` に `DbBase`(抽象クラス)と、その実装 `MySqliDb` / `PdoDb` があります。現在は `MySqliDb` を使用しています。
- アプリ側は `frames/logic/db_connection.php` の `cmdb_db()` を通して接続を取得します。1 リクエスト内では同じ接続が再利用され、終了時に自動で閉じられます。
- 主なメソッド

  | メソッド | 用途 |
  | --- | --- |
  | `ExecuteQuery($sql, $params)` | 複数行取得(連想配列の配列) |
  | `ExecuteSingle($sql, $params)` | 1 行取得(なければ `null`) |
  | `ExecuteScalar($sql, $params)` | 単一値取得(`COUNT(*)` など) |
  | `ExecuteNonQuery($sql, $params)` | INSERT / UPDATE / DELETE |
  | `BeginTransaction()` / `Commit()` / `Rollback()` | トランザクション |

- SQL は必ずプレースホルダ(`?`)とパラメータ配列で書きます。文字列連結で値を埋め込まないでください。

#### URL の組み立て

台帳機能ページへのリンクは `menu_loader.php` の `cmdb_page_url()` で生成します。

```php
cmdb_page_url('list', $currentCatalogId);
// => list.php?catalog_id=1
cmdb_page_url('register', $currentCatalogId, array('action' => 'edit', 'pk' => $id));
// => register.php?catalog_id=1&action=edit&pk=xxx
```

#### 処理時間計測ログ

`frames/logic/perf_log.php` の `cmdb_perf_mark('ラベル')` を呼ぶと計測ポイントが記録され、
リクエスト終了時に `logs/perf.log` へ 1 行追記されます(`session` → `db_connect` → `menu` → `embed` の区間時間と `total`)。
`PERF_LOG_ENABLED` が `false`(既定)のときは何も行いません。パフォーマンス調査時のみ有効にしてください。

#### コーディング規約(現状の慣習)

- 各ファイルの先頭に PHPDoc 形式のヘッダーコメント(概要、PHP version、改訂履歴、`@category` など)を記載する。変更時は【改訂履歴】に追記する。
- 出力は必ず `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')` を通す。
- Logic で例外を捕捉し、View には日本語のエラーメッセージ文字列(`$error`, `$regError` など)を渡す。
- 論理削除は `deleted_flag = 1` と `delete_dt = CURDATE()` で行い、一覧・検索では `deleted_flag = 0` で絞り込む。

### 1.2 global_config.php について

`frames/logic/global_config.php` はアプリ全体で共有する定数を `define()` で定義するファイルです。
`db_connection.php` と `perf_log.php` から `require_once` されるため、ログイン後のすべてのページで有効になります。

| 定数 | 意味 | 既定値 |
| --- | --- | --- |
| `DB_HOST` | MySQL ホスト | `localhost` |
| `DB_NAME` | データベース名 | `mcafeCMDB` |
| `DB_USER` | DB ユーザー | `mcafeCMDB_admin` |
| `DB_PASS` | DB パスワード | (ファイル参照) |
| `MENU_CACHE_TTL` | サイドバーメニュー(台帳・機能一覧)のセッションキャッシュ有効期間(秒) | `300` |
| `PERF_LOG_ENABLED` | 処理時間計測ログの ON/OFF(未定義なら `false`) | 未定義 |
| `PERF_LOG_PATH` | 計測ログの出力先(未定義なら `logs/perf.log`) | 未定義 |

運用上の注意:

- 環境ごとに異なる値(DB 接続情報)はこのファイルだけを変更すれば済むようにしています。他のファイルに接続情報を書かないでください。
- DB パスワードが平文で含まれるため、リポジトリの公開範囲に注意してください。
- `PERF_LOG_ENABLED` / `PERF_LOG_PATH` は `perf_log.php` 側に `if (!defined(...)) define(...)` のフォールバックがあるため、
  必要なときだけ `global_config.php` に `define('PERF_LOG_ENABLED', true);` を追記して有効化します。
- `MENU_CACHE_TTL` の間は `CMDB_M_CATALOGS` / `CMDB_M_CATALOG_FUNCTIONS` の変更がサイドバーに反映されません。
  台帳を追加・変更した直後に確認する場合は、一度ログアウト→ログインしてください。

### 1.3 CMDB_M_SYSPARAM について

`CMDB_M_SYSPARAM` は台帳ごとの選択肢(プルダウン・チェックボックスの候補)などのシステムパラメータを保持するマスタです。
プログラム側からは次の列を参照しています。

| 列 | 用途 |
| --- | --- |
| `catalog_name` | どの台帳のパラメータか。`CMDB_M_CATALOGS.catalog_name` と同じ文字列で結び付ける |
| `parameter_name` | パラメータの種別名(例: `区分`, `サービス`, `メーラー`) |
| `value` | 選択肢の値(画面にはそのまま表示され、そのまま保存される) |
| `display_order` | 表示順 |

取得方法(`catalogs/account/logic/register_logic.php` より):

```php
$sql = <<<SQL
SELECT value
FROM CMDB_M_SYSPARAM
WHERE catalog_name = ? AND parameter_name = ?
ORDER BY display_order
SQL;
$rows = $db->ExecuteQuery($sql, array($catalogName, '区分'));
```

現在の利用箇所(アカウント管理台帳):

| `parameter_name` | 画面上の用途 |
| --- | --- |
| `区分` | プライマリアカウントの「区分」プルダウン |
| `サービス` | 利用サービス行の「サービス名」プルダウン |
| `メーラー` | 利用メーラー(MUA)のチェックボックス |

注意:

- `catalog_name` で結び付けているため、`CMDB_M_CATALOGS.catalog_name` を変更した場合は `CMDB_M_SYSPARAM.catalog_name` も合わせて変更する必要があります。
- 選択肢の追加・削除・並び替えは DB の行を編集するだけで反映されます(プログラム変更は不要)。
- 既に登録済みのデータに使われている `value` を削除すると、編集画面でその値が選択状態にならなくなります。

### 1.4 セッション変数一覧

`session_start()` は `menu_loader.php`、`login_logic.php`、`logout_logic.php`、`mypage_logic.php` の先頭で呼ばれます。

| キー | 型 | 設定箇所 | 内容 |
| --- | --- | --- | --- |
| `$_SESSION['current_user']` | array | `login_logic.php`(ログイン成功時) | ログインユーザー情報。存在しなければ未ログインとみなす |
| 　`['user_id']` | int | | `CMDB_M_USER.user_id` |
| 　`['full_name']` | string | | 氏名 |
| 　`['nick_name']` | string | `mypage_logic.php` で更新 | ヘッダー右上に表示するニックネーム |
| 　`['logon_id']` | string | | ログオン ID |
| `$_SESSION['menu_cache']` | array | `menu_loader.php` | サイドバーメニューのキャッシュ |
| 　`['expires']` | int | | 有効期限(UNIX 時刻)。`time() + MENU_CACHE_TTL` |
| 　`['catalogs']` | array | | `CMDB_M_CATALOGS` の有効行(`invalid = 0`、`display_order` 順) |
| 　`['functions']` | array | | `catalog_id => CMDB_M_CATALOG_FUNCTIONS 行の配列` |

- パスワードはセッションに保持しません。
- ログアウト時(`logout_logic.php`)にすべて破棄されます。
- 台帳側(`catalogs/`)のロジックでは `$_SESSION` を直接参照せず、`menu_loader.php` / `catalog_loader.php` が用意した変数(`$user`, `$currentCatalogId` など。2.2 参照)を使用してください。

---

## 2. 新しい台帳の追加手順

台帳の追加は「マスタテーブルへの行追加」と「`catalogs/` 配下へのファイル追加」の 2 つで完了します。
共通フレーム(`frames/`)の変更は不要です。

手順の概要:

1. 台帳用のテーブルを作成する(DDL は別途)
2. `CMDB_M_CATALOGS` に台帳の行を追加する
3. `CMDB_M_CATALOG_FUNCTIONS` に機能(search / list / register)の行を追加する
4. 必要なら `CMDB_M_SYSPARAM` に選択肢を追加する
5. `catalogs/<working_dir 相当>/` に台帳側コントローラ・Logic・View を作成する
6. ログアウト→ログインしてサイドバーに表示されることを確認する

### 2.1 CMDB_M_CATALOGS と CMDB_M_CATALOG_FUNCTIONS

#### CMDB_M_CATALOGS(台帳マスタ)

サイドバーの「CATALOGS」に表示される台帳の一覧です。プログラムが参照する列は次の通りです。

| 列 | 用途 |
| --- | --- |
| `catalog_id` | 台帳 ID。URL の `catalog_id=` に使用される |
| `catalog_name` | 表示名。ページタイトル・サイドバー・`CMDB_M_SYSPARAM.catalog_name` の結合キー |
| `working_dir` | 台帳側ファイルの置き場所。ドキュメントルートからの相対パス(例: `catalogs/account`) |
| `display_order` | サイドバーでの表示順 |
| `invalid` | `0` = 有効、`0` 以外 = 無効(サイドバーに表示されず、URL を直接叩いても「指定された機能が見つかりません。」になる) |

例:

```sql
INSERT INTO CMDB_M_CATALOGS (catalog_id, catalog_name, working_dir, display_order, invalid)
VALUES (3, '機器台帳', 'catalogs/device', 3, 0);
```

#### CMDB_M_CATALOG_FUNCTIONS(台帳機能マスタ)

各台帳のサイドバー子メニュー(機能)を定義します。

| 列 | 用途 |
| --- | --- |
| `catalog_id` | 対象台帳 |
| `display_order` | 子メニューでの表示順 |
| `function_name` | 子メニューの表示名・ページタイトルの括弧内(例: `条件検索`) |
| `page_controller` | 呼び出す台帳側コントローラのファイル名。**`search.php` / `list.php` / `register.php` のいずれか**(親ページと同名である必要がある) |
| `page_parameter` | 台帳側コントローラに渡すクエリ引数の宣言(後述) |

`page_parameter` の書式は `名前=型&名前=型` で、型は `%s`(文字列)または `%i`(整数)です。
ここに宣言した名前のクエリ引数のみが `$_GET` から取り出され、型変換されて `$pageParams` に格納されます。
宣言されていない引数は `$pageParams` には入りません(必要なら台帳側で `$_GET` を直接参照します。`search` のキーワードはこの方式)。

また、サイドバーの `sidebar.php` は `page_parameter` に `action=%s` が含まれていると、リンクに `action=new` を自動で付与します
(「新規登録」メニューから登録画面を新規モードで開くため)。

例(アカウント管理台帳と同じ構成):

```sql
INSERT INTO CMDB_M_CATALOG_FUNCTIONS (catalog_id, display_order, function_name, page_controller, page_parameter) VALUES
(3, 1, '条件検索', 'search.php',   ''),
(3, 2, '一覧表示', 'list.php',     ''),
(3, 3, '新規登録', 'register.php', 'action=%s&pk=%s');
```

この設定で生成される URL:

| メニュー | URL |
| --- | --- |
| 条件検索 | `search.php?catalog_id=3` |
| 一覧表示 | `list.php?catalog_id=3` |
| 新規登録 | `register.php?catalog_id=3&action=new` |
| (一覧・検索結果の「編集」ボタン) | `register.php?catalog_id=3&action=edit&pk=<主キー>` |

### 2.2 catalogs フォルダ以下の使い方

`working_dir` で指定したフォルダに、次の構成でファイルを作成します(`catalogs/mail/` が最小構成の参考実装です)。

```
catalogs/device/
├─ search.php              require_once __DIR__ . '/logic/search_logic.php';
├─ list.php                require_once __DIR__ . '/logic/list_logic.php';
├─ register.php            require_once __DIR__ . '/logic/register_logic.php';
├─ logic/
│   ├─ search_logic.php
│   ├─ list_logic.php
│   └─ register_logic.php
└─ views/
    ├─ search_view.php
    ├─ list_view.php
    └─ register_view.php
```

#### 台帳側コントローラに渡される変数

`catalog_loader.php` が `require` する時点で、次の変数がスコープ内に存在します。

| 変数 | 内容 |
| --- | --- |
| `$currentCatalogId` | 台帳 ID(int) |
| `$currentCatalogName` | 台帳名(`CMDB_M_CATALOGS.catalog_name`) |
| `$currentPage` | 親ページ名 `'search'` / `'list'` / `'register'` |
| `$pageParams` | `page_parameter` で宣言されたクエリ値(型変換済み) |
| `$embedUrl` | 自ページの URL(`catalog_id` 付き)。フォームの `action` に使用する |
| `$user` | ログインユーザー(`$_SESSION['current_user']`) |
| `$catalogs` / `$catalogFunctions` | サイドバー用のメニュー情報(通常は使用しない) |

利用できる関数: `cmdb_db()`, `cmdb_page_url()`, `cmdb_perf_mark()`

#### Logic の書き方

```php
<?php
require_once __DIR__ . '/../../../frames/logic/db_connection.php';

$rows = array();
$listError = '';

try {
    $db = cmdb_db();
    $rows = $db->ExecuteQuery('SELECT ... FROM CMDB_CAT_XXX WHERE deleted_flag = 0 ORDER BY ...');
} catch (\Exception $e) {
    $listError = '一覧の取得に失敗しました。';
}

require_once __DIR__ . '/../views/list_view.php';
```

- `db_connection.php` は `require_once` のため二重読み込みの心配はありません。`session_start()` や認証確認は親側で済んでいるので書かないでください。
- `register_logic.php` では `$pageParams['action']`(`new` / `edit`)と `$pageParams['pk']` でモードと編集対象を判定します。
  POST 時は `$_POST['action']` / `$_POST['pk']`(hidden)から取得します。
- 登録・更新が成功したら `header('Location: ' . cmdb_page_url('list', $currentCatalogId)); exit;` で一覧に戻します。
  出力は `ob_start()` でバッファリングされているため、台帳側 Logic からの `header()` によるリダイレクトは問題なく動作します。
- 複数テーブルを更新する場合は `BeginTransaction()` / `Commit()` / `Rollback()` で囲みます(`catalogs/account/logic/register_logic.php` 参照)。

#### View の書き方

- `<html>` / `<head>` / `<body>` は出力しない(親ページの `catalog_page_view.php` が出力する)。AdminLTE の `<div class="box">` から始める。
- jQuery / Bootstrap / DataTables は親ページで読み込み済み。`list_view.php` で DataTables を使う場合は `catalogs/account/views/list_view.php` を参照。
- フォームの送信先は `$embedUrl`、他機能へのリンクは `cmdb_page_url()` を使う。相対パスを直書きしない。
- 検索フォーム(GET)には `<input type="hidden" name="catalog_id" value="...">` を含める(自ページに戻るため)。
- 出力値は必ず `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')` を通す。
- 台帳固有の CSS / JavaScript が必要な場合は View の末尾に `<style>` / `<script>` を直接記述する(`register_view.php` 参照)。

#### 確認

1. `php -l` で構文チェック。
2. ログアウト→ログイン(メニューキャッシュをクリア)し、サイドバーに台帳と機能が表示されることを確認。
3. 「指定された機能が見つかりません。」と表示される場合は `working_dir` のパス、`page_controller` のファイル名、`invalid` の値を確認。
4. 「画面の読み込みに失敗しました。」と表示される場合は台帳側 Logic で捕捉されていない例外が発生しています。

---

## 3. 運用メモ

- お知らせ文の変更: ドキュメントルートの `information.txt` を編集するとダッシュボードに反映されます。
- ユーザー追加: `CMDB_M_USER` に行を追加します。`passwd` には `SHA2('平文', 256)` の値(小文字 16 進 64 桁)を入れ、`status = 1` にします。
  ログイン後、ユーザー自身が `mypage.php` からニックネームとパスワードを変更できます。
- `asset/` 配下は AdminLTE 配布物です。バージョンアップ時は `frames/views/catalog_page_view.php`, `dashboard_view.php`, `login_view.php`, `mypage_view.php` の読み込みパスを確認してください。
- `logs/*.log` は Git 管理外です。`PERF_LOG_ENABLED` を有効にする場合は Web サーバーの実行ユーザーが `logs/` に書き込めることを確認してください。
