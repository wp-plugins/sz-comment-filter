=== Sz Comment Filter ===
Contributors: SzMake
Donate link: http://wp.szmake.net/donate/
Tags: comments, spam, bot, ajax, token
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 1.0.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

No spam in comments. blocked by Invisible internal token-code with ajax.This is not used CAPTCHA.

== Description ==

No spam in comments. blocked by Invisible internal token-code with ajax.

this plugin blocks 100% of automatic spam messages from spam-bots without CAPTCHA.

(but the commnet which is posted by spammers manually via browser is not blocked by this plugin)

There is no modification of display the comment form.

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

The blocking function is implemented by JavaScript(AJAX) and invisible 2 input forms.


= What is the first invisible input-form? =

The first input-form is input token-code by JavaScript.When "post comment" button was pushed, AJAX goes to have token-code.
This fields is hidden by JavaScript.
The spam-bots can not set valid token-code. - the comment will be blocked because it is spam-bots.

If the spammer will fill this trap-field with anything - the comment will be rejected because it is spam-bots.

= What is the secound invisible input-form? =

The secound input-form is honey pot fields.this fields is hidden by css-define.
This field is hidden for the user and user will not input to it.so it's empty everytime.
But spam-bots is tricked, and something is input - the comment will be rejected because it is spam-bots.

= How do I view the results? =

When you choose 'Comments' in the admin menu, it's shown report. 
it is displayed count of blocked. and show the rejected post-data.(The latest 10 case)

= Does the log data becomes too large? =

the log data are max 10 records.It's overwritten from old data.


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

= 1.0 =
* The first release.

== Contact ==

email to contact[at]szmake.net
twitter @sxmtz


