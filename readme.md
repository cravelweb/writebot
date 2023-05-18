# Writebot AI

Writebot AIは、OpenAI API (ChatGPT) を使ってWordPressの投稿記事を自動生成するWprdPressプラグインです。

Writebot AI is a WordPress plugin that uses the OpenAI API (ChatGPT) to automatically generate WordPress post content.

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

Cravel（クラベル）： https://github.com/cravelweb


## 基本的な使い方 Basic Usage

### WordPressへのインストール Installation on WordPress

プロジェクトファイルをzip形式でダウンロード（Download ZIP メニュー）し、このzipファイルをWordPress管理画面のプラグインの「新規追加」メニューよりインストールしてください。

インストールが完了すると、WordPress管理画面の「設定」メニュー内に「WriteBot AI設定」が追加されます。

Download the project file in zip format (Download ZIP menu), and install this zip file from the 'Add New' menu of the WordPress admin panel's plugins.

Once installed, 'WriteBot AI Settings' will be added to the 'Settings' menu in the WordPress admin pane


### 初期設定 Initial settings

「WriteBot AI設定」メニューをクリックすると設定画面が表示されます。

設定画面の「Open AI API KEY」ボックスに、Open AIから取得したAPIキー「sk-....」を入力してください。

上記を入力し、設定を反映すると「APIモデル」ボックスに利用可能なOpen AIのモデルが表示されます。この一覧から利用するモデルを選択し、再度設定を保存してください。

通常であれば「gpt-3.5-turbo (ChatGPT)」もしくは「gpt-4」を利用するのが一般的です。

(GPT-4 APIはアカウントにより利用権限の有無が異なるため、あなたのアカウント（APIキー）で利用できない場合は表示されません。)


※Open AIのAPI利用には料金がかかります。

When you click on the 'WriteBot AI Settings' menu, the settings screen will be displayed.

In the 'Open AI API KEY' box of the settings screen, please enter the API key 'sk-....' that you have obtained from Open AI.

After entering the above and applying the settings, the available Open AI models will be displayed in the 'API Model' box. Select the model you want to use from this list and save the settings again.

Usually, it is common to use 'gpt-3.5-turbo (ChatGPT)' or 'gpt-4'.

(Note that the GPT-4 API may not be available for your account (API key).)


*There is a fee for using the Open AI API.


### コンテンツの自動生成 Automatic generation of content

コンテンツの生成は投稿の編集画面から行います。投稿の新規作成などから編集画面を開くと画面下のメタボックスエリアに「Writebot AI」エリアが表示されます。

![コンテンツ生成画面](https://github.com/cravelweb/writebot/blob/main/img/manual_001.png)

ここで必要な設定を行い「この設定で生成する」ボタンをクリックすることで、ChatGPTのAPIを使ってテキストが生成されます。

なお、設定は複数項目ありますが、ChatGPTは全ての設定項目を厳密に守るわけではありません。

指示自体はChatGPTに正しく伝えられていても、その指示とはことなる内容でテキストが生成されることもあります。

あくまでコンテンツ生成の際のガイドラインやゆるい指示程度と思ってください。

また、設定項目はそのままChatGPTに送信されるプロンプトとして追加されます。プロンプトが長すぎるとエラーになってしまうので、必要なもののみ設定を行うことをお勧めします。

これらの設定項目は後述する「ゴーストライター設定（ghost.json）」データによってカスタマイズすることができます。

ご自身の環境でより自由な文書生成を行いたい場合はこれらのカスタマイズも含めて活用してください。

Content generation is done from the post editing screen. When you open the editing screen from creating a new post, etc., the 'Writebot AI' area will be displayed in the metabox area at the bottom of the screen.

Here, you can make the necessary settings and click the 'Generate with these settings' button to generate text using the ChatGPT API.

Although there are several setting items, ChatGPT does not strictly adhere to all setting items.

Even if the instructions are correctly communicated to ChatGPT, text may be generated that differs from those instructions.

Think of it as a guideline or loose instruction for content generation.

Also, the setting items are added as prompts that are sent directly to ChatGPT. If the prompt is too long, it will cause an error, so we recommend setting only the necessary items.

These setting items can be customized using the 'Ghostwriter Settings (ghost.json)' data mentioned later.

Please utilize these customizations as well if you want to create more free documents in your own environment.



## ゴーストライター設定（ghost.json）

コンテンツ生成画面ではいくつかの選択肢を組み合わせて文書生成を行うことができますが、これらの選択項目および選択項目に対応するプロンプトは外部ファイルを使って自由に変更することが可能です。

Writebot AIプラグインでは、この設定ファイルを「ゴーストライター」と呼んでいます。

ゴーストライター設定ファイルはプラグインの「/ghosts」ディレクトリに配置されており、プラグインは自動的に「ghost.json」ファイルを読み取るように動作しています。

※現在のプラグインのバージョンではゴーストライター設定ファイルの切り替え機能が実装されていませんが、今後WordPressの管理画面からもゴーストライター設定の切り替えやダウンロードなどに対応していければと考えています。

※英語環境でご利用の方は、「/ghosts」ディレクトリ内にある「ghost-en.json」ファイルの名前を「ghost.json」にリネームすることで、英語のゴーストライター（プロンプト）を利用することが可能です。

ゴーストライター設定ファイルはjson形式で構成されています。詳細は順次解説できればと思いますが、構造自体は単純なので同梱されているファイルを参考にカスタマイズしてください。

The content generation screen allows you to generate documents by combining several choices, but these choice items and the prompts corresponding to the choice items can be freely changed using an external file.

In the Writebot AI plugin, we call this configuration file a 'ghostwriter'.

The ghostwriter configuration file is located in the '/ghosts' directory of the plugin, and the plugin operates to automatically read the 'ghost.json' file.

*Although the current version of the plugin does not implement the ghostwriter configuration file switching function, we are considering supporting switching and downloading of ghostwriter settings from the WordPress admin panel in the future.

*If you are using it in an English environment, you can use the English


