## ダウンロード方法
* git cloneを行い、アプリケーションをインストールする方法
    - git clone https://github.com/panpanYuya/umarche.git
* git cloneでブランチを指定して、ダウンロードする場合
    - git clone -b ブランチ名　https://github.com/panpanYuya/umarche.git

* もしくはgit hubからZipファイルを取得して、ダウンロードしてください。

## インストール方法
cd laravel_umarche
composer install
npm install
npm run dev

.env.example　をコピーして .env　ファイルを作成
.envファイルの中の下記をご利用の環境に合わせて変更してください。

DB_CONNECTION = 使用するDB名
DB_HOST = ホストのDBのIPアドレス
DB_PORT = ホストのDBのポート番号
DB_DATABASE = 接続するDBのパスワード

開発環境または実行環境でDBを起動した後に
* php artisan migrate:fresh --seed

を実行する。(接続先のDBにテーブルとダミーデータが作成及び追加されていることを確認する。)

最後に
* php artisan key:generate

を実行する。(envファイルのAPP_KEYが作成される。composerでアプリケーションを作成した際には追加されるが、それ以外だと作成されないので、実施する)


## インストール後の実施事項
  - 画像のダミーデータについて
    * public/imagesフォルダ内にsample1.jpg ～ sample6.jpgとして保存しています。
    * php artisan storage:link　でstorageフォルダにリンク後、
    * storage/app/public/productsフォルダ内に保存すると表示されます。
    * (productsフォルダがない場合は作成してください。) 
    * ショップの画像を表示する場合はstorage/app/public/shopsフォルダを作成し画像を保存してください。

## section7の補足
決済のテストとしてstripeを利用しています。 必要な場合は .env にstripeの情報を追記してください。 (講座内で解説しています)

## section8の補足
メールのテストとしてmailtrapを利用しています。 必要な場合は .env にmailtrapの情報を追記してください。 (講座内で解説しています)

メール処理には時間がかかるので、 キューを使用しています。

必要な場合は php artisan queue:workで ワーカーを立ち上げて動作確認するようにしてください。 (講座内で解説しています)
