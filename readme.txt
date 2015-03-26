=== Sz Comment Filter ===
Contributors: SzMake
Donate link: http://wp.szmake.net/donate/
Tags: spam, spammer, comment, comments, comment-spam, block-spam, spambot, spam-bot, bot, token
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 1.1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

No spam in comments. blocked by Invisible internal token-code with ajax.This is not used CAPTCHA.

== Description ==

In English:  

No spam in comments. blocked by Invisible internal token-code with ajax.

This plugin blocks 100% of spam messages in the author of the environment.

It blocks spam without using the CAPTCHA input-field.

There is no modification of display the comment form.

(but the commnet which is posted by spammers manually via browser is not blocked by this plugin)


In Japanese:  

このプラグインはコメント投稿時にAjaxを使ってスパムロボットによる投稿コメントか判別し自動でブロックするプラグインです。

作者の環境では、今のところこのプラグインで100％スパムBOT投稿がブロックできています。

一般的なスパム対策としてAkismetプラグインがありますがブロックされるのは９割程度で100%は止まりませんでした。

別の方法としてCAPTCHA系のプラグインを使いBot対策する手段もありますが、こちらはほぼ100%スパムBotからの投稿はブロックされますがユーザーに煩わしい確認文字入力に毎回協力してもらう必要がありました。 このプラグインでは、見えない入力欄を用意してコメント投稿時にjavascriptでCAPTCHA入力に変わる固有の確認トークン入力処理をで行うことでスパムBotからの投稿をブロックします。

利用ユーザーのコメントフォームの見え方は変わりません。 

(残念ながらこのプラグインではブラウザを介した手入力によるスパム投稿はブロックできません)

[日本語の詳細説明ページはこちら](http://wp.szmake.net/sz-comment-filtter/ "Documentation in Japanese")


= Translators =

* Japanese (ja)


== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Upload 'sz-comment-filter' folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

= Usage =

No setting is necessary.If it's Activated, it'll begin to block the spam comment.

When you choose 'Comments' in the admin menu, it's shown report. e.g."Total 3 spam comments were blocked" .
(If the report was not shown,then please check "Screen Options" section.)

== Frequently Asked Questions ==

= How does sz-comment-filter plugin block the spam comment? =

The blocking function is implemented by JavaScript(AJAX) and invisible 2 input field.


= What is the first invisible input-field? =

The first input-field is input token-code by JavaScript.When "post comment" button was pushed, AJAX goes to have token-code.
This fields is hidden by JavaScript.
The spam-bots can not set valid token-code. - the comment will be blocked because it is spam-bots.


= What is the second invisible input-field? =

The second input-field is honey pot fields.this fields is hidden by css-define.
This field is hidden for the user and user will not input to it.so it's empty everytime.
But spam-bots is tricked, and something is input - the comment will be rejected because it is spam-bots.

= How do I view the results? =

When you choose 'Comments' in the admin menu, it's shown report. 
it is displayed count of blocked. and show the rejected post-data.(The latest 10 case)

= Does the log data becomes too large? =

The log data are max 10 records.It's overwritten from old data.


= What about trackback post? =

The trackbacks are blocked everytime.
You may enable trackbacks if you use it.
Edit the sz-comment-filter.php file and find "$szmcf_settings" and "allow_trackbacks" of elements change to "true".

= Can the user post cooment with JavaScript disabled browser? =

User can post comment without JavaScript.when must be enter token-code manualy.


== Screenshots ==

1. The reports of blocked spam-post. 
2. The display which is JavaScript disabled browser.

== Changelog ==

= 1.1.2 =
* [FIX] css for display the blocking log(FromIP).

= 1.1.1 =
* [FIX] readme.txt markdown format.

= 1.1.0 =
* Modified display the blocking log in admin menu.
* Fixed the naming of the function name.
* (ja)ブロックしたコメント履歴の表示修正
* (ja)関数名の命名を修正

= 1.0.0 =
* The first release.
* (ja)初回版リリース

== Contact ==

email to contact[at]szmake.net  
twitter @sxmtz  


