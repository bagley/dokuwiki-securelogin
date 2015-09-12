<?php
/**
 * Helper Component for Securelogin Dokuwiki Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Mikhail I. Izmestev, Matt Bagley <securelogin@mattfiddles.com>
 *
 * @see also   https://www.dokuwiki.org/plugin:securelogin
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * This is the base class for all syntax classes, providing some general stuff
 */
class helper_plugin_securelogin extends DokuWiki_Plugin {
	protected $_keyFile;
	protected $_keyIFile;
	protected $_key = null;
	protected $_keyInfo = null;
	protected $_workCorrect = false;
	protected $_canWork = false;
	
	/**
	 * constructor
	 */
	function __construct() {
		global $conf;

		$this->_keyIFile = $conf['cachedir'].'/securelogin.ini';
		$this->_keyFile = $conf['cachedir'].'/securelogin.key';

		if( true 
			&& function_exists("openssl_pkey_export_to_file")
			&& function_exists("openssl_pkey_get_private")
			&& function_exists("openssl_pkey_new")
			&& function_exists("openssl_private_decrypt")
			)
			$this->_canWork = true;
	}
	
	function canWork() {
		return $this->_canWork;
	}

	function haveKey($onlyPublic = false) {
		if($onlyPublic) {
			if($this->_keyInfo)	return true;

			if(file_exists($this->_keyIFile)) {
				$this->_keyInfo = parse_ini_file($this->_keyIFile);
				return true;
			}
		}
		
		if(!$this->_key && file_exists($this->_keyFile)) {
			$this->_key = openssl_pkey_get_private(file_get_contents($this->_keyFile));
			if($this->_key) {
				if(file_exists($this->_keyIFile))
					$this->_keyInfo = parse_ini_file($this->_keyIFile);
				else
					$this->savePublicInfo($this->getPublicKeyInfo(file_get_contents($this->_keyFile)));
			}
		}
		return null != $this->_key;
	}
	
	function getKeyLengths() {
		return array('default' => 'default', '512' => '512', '1024' => '1024', '2048' => '2048');
	}
	
	function generateKey($length) {
		if(!array_key_exists($length, $this->getKeyLengths())) {
			msg("Error key length $length not supported", -1);
			return;
		}
		
		$newkey = @openssl_pkey_new(('default' == $length)?array():array('private_key_bits' => intval($length)));

		if(!$newkey) {
			msg('Error generating new key', -1);
			return; 
		}
		if(!openssl_pkey_export_to_file($newkey, $this->_keyFile))
			msg('Error export new key', -1);
		else {
			$this->_key = openssl_pkey_get_private(file_get_contents($this->_keyFile));
			$this->savePublicInfo($this->getPublicKeyInfo(file_get_contents($this->_keyFile)));
		}
	}
	
	function getKeyLength() {
		return (strlen($this->getModulus())-2)*4;
	}
	
	function getModulus() {
		return ($this->haveKey(true))?$this->_keyInfo['modulus']:null;
	}
	
	function getExponent() {
		return ($this->haveKey(true))?$this->_keyInfo['exponent']:null;
	}
	
	function savePublicInfo($info) {
		$fpinfo = fopen($this->_keyIFile, "w");
		foreach($info as $key => $val) {
			fprintf($fpinfo, "%s=\"%s\"\n", $key, $val);
		}
		fclose($fpinfo);
		$this->_keyInfo = parse_ini_file($this->_keyIFile);
	}
	
	function decrypt($text) {
		if($this->haveKey())
			openssl_private_decrypt(base64_decode($text), $decoded, $this->_key);
		return $decoded;
	}
		
	function decodeBER($bin) {
		function my_unpack($format, &$bin, $length) {
			$res = unpack($format, $bin);
			$bin = substr($bin, $length);
			return $res;
		}	
		
		function readBER(&$bin) {
			if(!strlen($bin)) return FALSE;
			
			
			$data = my_unpack("C1type/c1length", $bin, 2);
			
			if($data[length] < 0) {
				$count = $data[length] & 0x7F;
				$data[length] = 0;
				while($count) {
					 $data[length] <<= 8;
					 $tmp = my_unpack("C1length", $bin, 1);
					 $data[length] += $tmp[length];
					 $count--;
				}
			}
			
			switch($data[type]) {
				case 0x30:	
					$data[value] = array();
					do {
						$tmp = readBER($bin);
						if($tmp)
							$data[value][] = $tmp; 
					} while($tmp);
					break;
				case 0x03:
					$null = my_unpack("C1", $bin, 1);
					$data[value] = readBER($bin);
					break;
				case 0x04:
					$data[value] = readBER($bin);
					break;
				default: 
					$count = $data[length];
					while($count) {
						$tmp = my_unpack("C1data", $bin, 1);
						$data[value] .= sprintf("%02X", $tmp[data]);
						$count--;
					}
			}
			
			return $data;
		}
		
		return readBER($bin); 
	}
	
	function getPublicKeyInfo($pubkey) {
		function findKeyInfo($data, &$pubkeyinfo) {
			if($data[type] == 48) {
				if(count($data[value]) != 9) {
					foreach($data[value] as $subdata) {
						if(findKeyInfo($subdata, $pubkeyinfo))
							return true;
					}
				}
				else {
					$pubkeyinfo = array(
						"modulus" => $data[value][1][value],
						"exponent" => $data[value][2][value],
					);
					return true;
				}
			}
			elseif($data[type] == 4) {
				return findKeyInfo($data[value], $pubkeyinfo);
			}
			return false;
		}

		$pubkey = preg_split("(-\n|\n-)", $pubkey);
		$binary = base64_decode($pubkey[1]);
		
		$data = $this->decodeBER($binary);
		
		findKeyInfo($data, $pubkeyinfo);
/*		
		$pubkeyinfo = array(
			"modulus" => $data[value][1][value][2][value][value][1][value],
			"exponent" => $data[value][1][value][2][value][value][2][value],
		);
*/		
		return $pubkeyinfo;	
	}
	
	function workCorrect($yes = false) {
		if($yes)
			$this->_workCorrect = true;
		return $this->_workCorrect;
	}
}
