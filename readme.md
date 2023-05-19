# Writebot AI

Writebot AIは、OpenAI API (ChatGPT) を使ってWordPressの投稿記事を自動生成するWordPressプラグインです。

日本語および英語環境に対応しています。


Writebot AI is a WordPress plugin that uses the OpenAI API (ChatGPT) to automatically generate WordPress post content.

This plugin works in Japanese and English WordPress environments.




## ライセンス License

Writebot AIプラグインは 100％GPL です。

配布物に含まれているPHP、JavaScript、CSS、jsonデータ等のすべてをGPLとして公開します（100%GPL）。


Writebot AIプラグインの再配布もしくは、Writebot AIプラグインを基盤として作成したプログラムを配布する場合は、配布物すべて（PHP、JavaScript、CSS、jsonデータ、その他同梱物）をGPLとして公開してください。

（100％GPL：無料、有料問わず）


The Writebot AI plugin is 100% GPL.

All components included in the distribution, such as PHP, JavaScript, CSS, and json data, are released as GPL (100% GPL).


If you redistribute the Writebot AI plugin or distribute a program created based on the Writebot AI plugin, please release all distribution materials (PHP, JavaScript, CSS, json data, and other bundled items) as GPL.

(100% GPL: free, paid)


License : [GNU General Public License](http://www.gnu.org/licenses/gpl-2.0.html)




## 開発者 Developer

Cravel（クラベル）： [Github](https://github.com/cravelweb), [Blog](https://cravelweb.com/), [Buy Me A Coffee](https://www.buymeacoffee.com/cravel)




## 基本的な使い方 Basic Usage

### WordPressへのインストール Installation on WordPress

プロジェクトファイルをzip形式でダウンロード（Download ZIP メニュー）し、このzipファイルをWordPress管理画面のプラグインの「新規追加」メニューよりインストールしてください。

インストールが完了すると、WordPress管理画面の「設定」メニュー内に「WriteBot AI 設定」が追加されます。


Download the project file in zip format (Download ZIP menu), and install this zip file from the 'Add New' menu of the WordPress admin panel's plugins.

Once installed, 'WriteBot AI settings' will be added to the 'Settings' menu in the WordPress admin pane




### 初期設定 Initial settings

「WriteBot AI 設定」メニューをクリックすると設定画面が表示されます。

設定画面の「Open AI API KEY」ボックスに、Open AIから取得したAPIキー「sk-....」を入力してください。

上記を入力し、設定を反映すると「APIモデル」ボックスに利用可能なOpen AIのモデルが表示されます。この一覧から利用するモデルを選択し、再度設定を保存してください。

通常であれば「gpt-3.5-turbo (ChatGPT)」もしくは「gpt-4」を利用するのが一般的です。

(GPT-4 APIはアカウントにより利用権限の有無が異なるため、あなたのアカウント（APIキー）で利用できない場合は表示されません。)

※Open AIのAPI利用には料金がかかります。


When you click on the 'WriteBot AI Settings' menu, the settings screen will be displayed.

In the 'Open AI API KEY' box of the settings screen, please enter the API key 'sk-....' that you have obtained from Open AI.

After entering the above and applying the settings, the available Open AI models will be displayed in the 'API Model' box. Select the model you want to use from this list and save the settings again.

Usually, it is common to use 'gpt-3.5-turbo (ChatGPT)' or 'gpt-4'.

(Note that the GPT-4 API may not be available for your account.)


*There is a fee for using the Open AI API.




### コンテンツの自動生成 Automatic generation of content

コンテンツの生成は投稿の編集画面から行います。投稿の新規作成などから編集画面を開くと画面下のメタボックスエリアに「Writebot AI」エリアが表示されます。

![コンテンツ生成画面](https://github.com/cravelweb/writebot/blob/main/img/manual_001.png)

ここで必要な設定を行い「この設定で生成する」ボタンをクリックすることで、ChatGPTのAPIを使ってテキストが生成されます。

生成されたテキストはメタボックスエリア内のテキストボックスに格納されます。

このテキストボックス自体は特に投稿の内容表示などに連動するものではないため、生成したテキストを投稿本文に利用する場合はコピー＆ペーストして利用してください。

なお、メタボックスの設定は投稿ページ単位で保存されますので、投稿ごとに生成したテキストやプロンプトの設定などもそのまま一時保存しておくことが可能です。


Content generation is done from the post editing screen. When you open the editing screen from creating a new post, etc., the 'Writebot AI' area will be displayed in the metabox area at the bottom of the screen.

Here, you can make the necessary settings and click the 'Generate with these settings' button to generate text using the ChatGPT API.

The generated text is stored in a textbox within the metabox area.

Since this text box itself is not particularly linked to the content display of the post, please copy and paste the generated text when using it in the post body.

Meta box settings are saved for each post page, so you can temporarily save the text and prompt settings generated for each post as they are.




## 生成コンテンツについて About generated content

ゴーストライターの設定項目は複数ありますが、ChatGPTは全ての設定項目を厳密に守るわけではありません。

指示自体はChatGPTに正しく伝えられていても、その指示とはことなる内容でテキストが生成されることもあります。

あくまでコンテンツ生成の際のガイドラインやゆるい指示程度と思ってください。

また、設定項目はそのままChatGPTに送信されるプロンプトとして追加されます。プロンプトが長すぎるとエラーになってしまうので、必要なもののみ設定を行うことをお勧めします。

これらの設定項目は後述する「ゴーストライター設定（ghost.json）」データによってカスタマイズすることができます。

ご自身の環境でより自由な文書生成を行いたい場合はこれらのカスタマイズも含めて活用してください。


Although there are several setting items, ChatGPT does not strictly adhere to all setting items.

Even if the instructions are correctly communicated to ChatGPT, text may be generated that differs from those instructions.

Think of it as a guideline or loose instruction for content generation.

Also, the setting items are added as prompts that are sent directly to ChatGPT. If the prompt is too long, it will cause an error, so we recommend setting only the necessary items.

These setting items can be customized using the 'Ghostwriter Settings (ghost.json)' data mentioned later.

Please utilize these customizations as well if you want to create more free documents in your own environment.




## ゴーストライター設定（ghost.json）

コンテンツ生成画面ではいくつかの選択肢を組み合わせて文書生成を行うことができますが、これらの選択項目および選択項目に対応するプロンプトは外部ファイルを使って自由に変更することが可能です。

Writebot AIプラグインでは、この設定ファイルを「ゴーストライター」と呼んでいます。

ゴーストライター設定ファイルは基本的なプロンプトをまとめた日本語用と英語用の2種類を同梱しており、切り替えはWritebot AIの設定画面から行うことができます。

また、ゴーストライター設定ファイルはプラグインの「/ghosts」ディレクトリに配置されており、プラグインは自動的にこのディレクトリ内のゴーストライターファイルをリストアップします。

オリジナルのゴーストライター設定ファイルを用意することで、更に自由なプロンプトをChatGPTに与えて文書生成を効率化させることも可能です。

※現在のバージョンではゴーストライター設定ファイルの追加はFTPなどを使って行う必要がありますが、今後WordPressの管理画面からもゴーストライターの追加ダウンロードや配布に対応していければと考えています。

ゴーストライター設定ファイルはjson形式で構成されています。詳細は順次解説できればと思いますが、構造自体は単純なので同梱されているファイルを参考にカスタマイズしてください。

参考までに、ゴーストライター設定用のjsonのschemaを/json/フォルダ配下に同梱しています。

[schema](https://github.com/cravelweb/writebot/blob/main/json/ghost-schema.json)


In the content generation screen, you can create a document by combining several options, but these selection items and the prompts corresponding to the selection items can be freely changed using an external file.

In the Writebot AI plugin, we call this setting file a "Ghostwriter".

The Ghostwriter setting file comes with two types, one for Japanese and one for English, which encapsulate basic prompts. You can switch between these from the Writebot AI settings screen.

Also, the Ghostwriter setting file is placed in the "/ghosts" directory of the plugin, and the plugin automatically lists the Ghostwriter files in this directory.

By preparing an original Ghostwriter setting file, it is also possible to give more free prompts to ChatGPT and make document generation more efficient.

In the current version, you need to add the Ghostwriter setting file using FTP or similar, but we are considering supporting the addition, download, and distribution of Ghostwriters from the WordPress management screen in the future.

The Ghostwriter setting file is composed in json format. We hope to explain the details gradually, but the structure itself is simple, so please customize it by referring to the included file.