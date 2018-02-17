Securelogin Dokuwiki Plugin
==============

This plugin uses [Tom Wu's implementation of RSA algorithm in JavaScript](http://www-cs-students.stanford.edu/~tjw/jsbn/) on the client to encrypt the login password with the server's public key. The encrypted password is then sent to the server where it can be decrypted. Man-in-the-middle attacks are prevented by using a variable token (salt) added to the password before encrypting. Therefore, replay attacks don't work.

When securelogin is used, there is always a *use securelogin* checkbox near the password field. If the browser has no JavaScript or JavaScript is disabled, then obviously, the passwords are sent in clear text, as they are by default with DokuWiki. In this case though, the user *should* notice the absence of the checkbox.

Also, whenever a password has to be entered, it is automagically encrypted by this plugin, be it on the login, profile or the admin page.

Works with:
  * 2017-02-19 "Frusterick Manners"
  * 2016-06-26 "Elenor Of Tsort"
  * 2015-08-10 "Detritus"
  * 2014-09-29 "Hrun"
  
Download and Installation
--------------

  - Download and install the plugin using the [Plugin Manager](https://www.dokuwiki.org/plugin:plugin). You can search for "securelogin" to find the plugin in the Plugin Manager. Refer to [Plugins](https://www.dokuwiki.org/plugins) on how to install plugins manually.
  - Go the admin pages and select *securelogin*. Then click on the 'generate-new-key' button.
  - You're done. From then on, all passwords are encrypted before being sent.

For support for these older versions use [this link](https://github.com/bagley/dokuwiki-securelogin/archive/c1f0a0e018cedfd29a48ab157098efe480e37049.zip)
  * 2014-05-05 "Ponder Stibbons"
  * 2013-12-08 "Binky"
  * 2013-05-10a Weatherwax
  * 2012-10-13 Adora Belle

Changes
--------------
  * **20180217** Thanks to [Christian Paul](https://github.com/jaller94) for reporting
    * Fixed issue where second password was not encrypted on add/modify users

  * **20150928** Thanks to Satoshi Sahara
    * made compatible with DokuWiki 2015-08-10 "Detritus"
    * replace deprecated split() function call
    * prevent PHP error output
    * use PHP5 constructor method for classes
    * Improved coding style and added license header in source files
    
  * **20140923** Thanks to Hideaki SAWADA
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

