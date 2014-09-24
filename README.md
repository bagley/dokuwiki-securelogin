Securelogin Dokuwiki Plugin
==============

This plugin lets you login securely without HTTPS. It uses [Tom Wu's implementation of RSA algorithm in JavaScript](http://www-cs-students.stanford.edu/~tjw/jsbn/) on the client to encrypt the password with the servers public key. The passwords are sent encrypted over HTTP. No need for HTTPS. Man-in-the-middle attacks are prevented by using a variable token (salt) added to the password before encrypting. Therefore, replay attacks don't work.

With version 20091213 and + , whenever a password has to be entered, it is automagically encrypted by this plugin, be it on the
*login*, *profile* or *admin* page.

securelogin version 20091206 and + is compatible with the showlogin plugin.

When securelogin is used, there is always a *use securelogin* checkbox near the password field. If the browser has no JavaScript or JavaScript is disabled, then obviously, the passwords are sent in clear text, as they are by default with DokuWiki. In this case though, the user will notice the absence of the checkbox.

Works with:
  
  * 2014-05-05 "Ponder Stibbons"
  * 2013-12-08 "Binky"
  * 2013-05-10a Weatherwax
  * 2012-10-13 Adora Belle 


Download and Installation
--------------

  - Download and install the plugin using the [Plugin Manager](https://www.dokuwiki.org/plugin:plugin), please use the download link given above. Refer to [Plugins](https://www.dokuwiki.org/plugins) on how to install plugins manually.
  - Go the admin pages and select *securelogin*. Then click on the 'generate-new-key' button.
  - You're done. From then on, all passwords are encrypted before being sent.

Changes
--------------
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

