# Securelogin Dokuwiki Plugin

**Not Maintained** - *While it still works with the below versions, this repo has been archived. See the [Plugin page](https://www.dokuwiki.org/plugin:securelogin) for any updated details, patches, or for those who may wish to adopt it.*

This plugin uses [Tom Wu's implementation of the RSA algorithm in JavaScript](http://www-cs-students.stanford.edu/~tjw/jsbn/) on the client browser (before it leaves your computer) to encrypt the login password with the server's public key. The encrypted password is then sent to the server where it can be decrypted. Man-in-the-middle attacks are prevented by adding a variable token (salt) to the password before encrypting. Therefore, replay attacks don't work.

When Securelogin is used, there is always a `use securelogin` checkbox near the password field. If the browser has no JavaScript or JavaScript is disabled, then obviously, the passwords are sent in clear text, as they are by default with DokuWiki. In this case though, the user *should* notice the absence of the checkbox.

Also, whenever a password has to be entered, it is automagically encrypted by this plugin, be it on the login, profile, or admin pages.

In short, it takes your password:

```
p:MySecretPa$$word
```

And instead has the login/profile/admin page submit the password as:

```
securelogin:M66YMHFzjl9qXa96zr2JzDWlV3WTE+4mOgJZNNr3yW9xPzSORtSIjp+ZNczopNUp5N0M0ASiqutgf1nio+iTN....
```

### Works with

  * 2018-04-22b "Greebo"
  * 2017-02-19 "Frusterick Manners"
  * 2016-06-26 "Elenor Of Tsort"
  * 2015-08-10 "Detritus"
  * 2014-09-29 "Hrun"

### Uses RSA, which may be vulnerable to certain attacks

Attacks against RSA have become easier. This plugin uses RSA and needs to be rewritten to use a different library/encryption mechanism. As it is, it may be vulnerable to certain targeted man-in-the-middle attacks. Though it appears that those attacks may still be fairly expensive against a regular wiki site. If in doubt, see the next section.


### Please use HTTPS, CORS, and others

This plugin was made when HTTPS was pricey (for a wiki), but we still wanted as much security as we could get. Now that one can easily have HTTPS, CORS, [Subresource Integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity), etc, it's not as relevant. Consider it as just a possible extra layer of security. Your first priority should always be a good server setup with the latest in security. I've left this here for those that want it.

Because good security is like a onion. You want a lot of layers in order keep things protected even *when* some layers fail.

### CAPTCHA Plugin Login Issue

If the [CAPTCHA plugin](https://www.dokuwiki.org/plugin:captcha) is enabled on the login page with this plugin, the CAPTCHA will not be processed. ie, the user can enter whatever, and the login will be processed like normal. So Bots can attempt to login and ignore the CAPTCHA.

A wrong password will still fail. And Securelogin will still encrypt the password. The login will just act as if CAPTCHA is not installed. The CAPTCHA plugin should still work elsewhere on the site.

### Installation and Setup

  - Search for and install the plugin using the [Extension Manager](https://www.dokuwiki.org/plugin:extension).
  - Once installed, go the Admin page and select "Secure login configuration".
  - Under "Generate new key pair", click the "Generate" button.
  - Click the "Test" button to verify your setup. If all is working, a bubble will appear containing whatever was in the "Test Message" box.

You're done. From then on, all passwords are encrypted before being sent.

To manually install the plugin, please see the [manual install instructions](https://www.dokuwiki.org/plugin_installation_instructions). Then follow the last three steps above.

### Older Versions

> Don't use this unless you have to. It's not supported. It's better to upgrade your Installation.

For support for these older versions use [this version of Securelogin](https://github.com/dokuwiki-securelogin-archive/dokuwiki-securelogin/archive/c1f0a0e018cedfd29a48ab157098efe480e37049.zip) and install it manually.
  * 2014-05-05 "Ponder Stibbons"
  * 2013-12-08 "Binky"
  * 2013-05-10a Weatherwax
  * 2012-10-13 Adora Belle

## Details of how it works

Normally when you submit your 'MySecretPa$$word', you will see it in the data transfer:

```
sectok:
id:start
do:login
u:MyUser
p:MySecretPa$$word
```

You can easily see the 'MySecretPa$$word' in the above example.

But when you use this plugin, it will encrypt the password, which can only be decrypted on the server.

```
sectok:
id:start
do:login
u:MyUser
p:******
use_securelogin:1
securelogin:M66YMHFzjl9qXa96zr2JzDWlV3WTE+4mOgJZNNr3yW9xPzSORtSIjp+ZNczopNUp5N0M0ASiqutgf1nio+iTNj3pS24kHD1LZb6GcG7cFvpr/uzfxJsO8jAbFD6/ZkB0xy9vBMabn3BYP7GWLrTR3b/7zNdla/FdqjX9U48dHMrcO2/ZFJKLsdzt84/bC+3xoV7/qC/BZO5AbQ37SvLEC7DaMTMtbSqlF573Y0iOMb3wYe1rj2m/HQiBM8ro25OBfnUxmgJFMVVkfkLdNUepRjUeeJSXF+R5XDcO2L4uX9D8AOE8nSecRn+0gqwz6PzPPqEpv60y0Io1rZXevG+I9Q==
```

The javascript on the page takes the form's password variable `p=MySecretPa$$word`, encrypts it with the provided salt (that changes on each page load), and sets the result as `securelogin`. It also replaces `p`'s value with stars so it can't submit the password in the clear.

When the server receives the data, it sees that `use_securelogin` is set to `1` (true), so it knows the password was encrypted. It will decrypt the `securelogin` variable and separate it from the salt value. From this it gets the `p=MySecretPa$$word` value, which it sets so the Dokuwiki authentication routines have it. Dokuwiki can then compare the passwords like it normally does.

This same process happens during the add user, modify user, and edit profile options. This is what will be seen if someone views a user changing their password:

```
do:profile
fullname:MyUser
email:user@example.com
newpass:******
passchk:******
oldpass:******
use_securelogin:1
securelogin:mCUIwYbHRgNjmAkr1CHssH8g1ZAgGKIxsFsMZUN1XM703V2g4hB5upzfJeVyE/aT9ByOYxQChbhRyJezjD7jO4LKwlgBR/Jnqkr+rUr70MLcoRybM8maTGdAGDM3VweSylqAGOASKb87hKYb0URUFo+yfGaKp572IWCfSZDHLrP1Hrs/f7EYKXozXpMNHA3l/VXNm2wGAwvkvnfFgkRZonrdfdUlLDC0OkBpa3WawMqoYb+1/kcuGsBcAve0Tp+uMQZw8FwHj8SOp9kJLUnEqXrop2pXa3mc9j8NS54CeCbJuJ0qfEhUHIE9/BHUgbmCPQV6XNWttZbRp8r1Q1dG/g==
```

In this case, all three passwords are encrypted into `securelogin`, and the post values replaced with stars.

## Changelog

  * **20200527**
    * Updated url to archived location of repo.

  * **20200418**
    * Quoted array keys for php 7.2

  * **20180217** Thanks to [Christian Paul](https://github.com/jaller94) for reporting
    * Fixed issue where second password was not encrypted on add/modify users

  * **20150928** Thanks to [Satoshi Sahara](https://github.com/ssahara)
    * made compatible with DokuWiki 2015-08-10 "Detritus"
    * replace deprecated split() function call
    * prevent PHP error output
    * use PHP5 constructor method for classes
    * Improved coding style and added license header in source files

  * **20140923** Thanks to [Hideaki SAWADA](https://github.com/sawachan)
    * Japanese language files added

  * **20140417**
    * Changed download link per Mikhail I. Izmestev's [request](http://github.com/izmmisha/dokuwiki-securelogin/pull/1)
    * Updates to plugin info in admin page, like the website link and more unified info.

  * **20130519**
    * added jQuery patches. Thanks to Casper

  * **20101121**
    * add german translation. Thanks to Heiko Barth
    * fix finding pubkey info with openssl 0.9.8
    * fix escaping encoded data (now supports non ascii passwords)

  * **20101105**
    * fixed support php < 5.2
    * added plugin.info.txt

  * **20101101** Thanks to Christophe Martin
    * fix bug with some chars in passwords

  * **20091213**
    * add support of usermanager plugin

  * **20091206** Thanks to Christophe Martin
    * fix unclosed < div id="secure__login" >
    * add showlogin compat

  * **20090901** Thanks to Jan Hána
    * add Czech translation

  * **20090802** Thanks to Christophe Martin
    * fix problem with URL-rewrite DokuWiki method
    * add French translation
