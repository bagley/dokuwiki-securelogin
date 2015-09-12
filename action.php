<?php
/**
 * Action Component for Securelogin Dokuwiki Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mikhail I. Izmestev, Matt Bagley <securelogin@mattfiddles.com>
 *
 * @see also   https://www.dokuwiki.org/plugin:securelogin
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_securelogin extends DokuWiki_Action_Plugin {
    protected $slhlp;

    function __construct() {
        $this->slhlp = plugin_load('helper', $this->getPluginName());
    }

    /**
     * Register its handlers with the DokuWiki's event controller
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AUTH_LOGIN_CHECK', 'BEFORE',  $this, '_auth');
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE',  $this, '_ajax_handler');
    }

    function _auth(Doku_Event $event, $param) {
        $this->slhlp->workCorrect(true);
        if(!$this->slhlp || !$this->slhlp->canWork() || !$this->slhlp->haveKey(true)) return;
        
        if(isset($_REQUEST['use_securelogin']) && $_REQUEST['use_securelogin'] && isset($_REQUEST['securelogin'])) {
            list($request,) = explode(';', $this->slhlp->decrypt($_REQUEST['securelogin']));
            if($request) {
                foreach(explode("&", $request) as $var) {
                    list($key, $value) = explode("=",$var,2);
                    $value = urldecode($value);
                    $_REQUEST[$key] = $value;
                    $_POST[$key] = $value;
                }
            }
            unset($_REQUEST['securelogin']);
            unset($_REQUEST['use_securelogin']);
        }
        if($_REQUEST['do'] == "login") {
            auth_login($_REQUEST['u'], $_REQUEST['p'], $_REQUEST['r'], $_REQUEST['http_credentials']);
            $event->preventDefault();
        }
    }

    function _ajax_handler(Doku_Event $event, $param) {
        if($event->data != 'securelogin_public_key') return;
        if(!$this->slhlp || !$this->slhlp->canWork() || !$this->slhlp->haveKey(true)) return;

        header('Content-Type: text/javascript; charset=utf-8');
        print 'function encrypt(text) {
        var rsa = new RSAKey();
        rsa.setPublic("'.$this->slhlp->getModulus().'", "'.$this->slhlp->getExponent().'");
        var res = rsa.encrypt(text);
        if(res) {
                return hex2b64(res);
        }
        }
        var securelogin_login_label = "'.$this->getLang('use_securelogin').'";
        var securelogin_update_label = "'.$this->getLang('use_secureupdate').'";';

        $event->preventDefault();
        return;
    }
}
