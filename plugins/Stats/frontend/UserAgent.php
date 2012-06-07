<?php

/**
 * User Agent Class, Stats Plugin, Furasta.Org
 *
 * Used for deciphering the user agent string. Based on a
 * tutorial here: http://www.lynkit.net/blog/php-useragent-browser-os-detection-script/
 *
 * @author	Conor Mac Aoidh <conormacaoidh@gmail.com>
 * @version	1
 */

class UserAgent{
	public $UA;

	public function __construct( ){
		$this->UA = $_SERVER[ 'HTTP_USER_AGENT' ];
	}

	public static function xml2array($xml) {
		$xmlary = array();
		$reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
		$reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';
		preg_match_all($reels, $xml, $elements);

		foreach($elements[1] as $ie => $xx) {
			$xmlary[$ie]["name"] = $elements[1][$ie];

			if($attributes = trim($elements[2][$ie])) {
				preg_match_all($reattrs, $attributes, $att);
				foreach($att[1] as $ia => $xx) $xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];

				$cdend = strpos($elements[3][$ie], "<"); 				if($cdend > 0) $xmlary[$ie]["text"] = substr($elements[3][$ie], 0, $cdend - 1);

				if(preg_match($reels, $elements[3][$ie])) $xmlary[$ie]["elements"] = self::xml2array($elements[3][$ie]);
				elseif($elements[3][$ie]) $xmlary[$ie]["text"] = $elements[3][$ie];
			}
		}

	        return $xmlary;
	}

	public function lang($ua) {
		//Detect Language
		$ua = str_replace(")", ";", $ua);
		$parts = explode(";", $ua);
		foreach($parts as $p) {
			$p = trim($p);
			if(strlen($p)===5&&strpos($p, '-')) return strtoupper(substr($p,0,2));
		}
		return false;
	}

	public function detect($xmlfile = "ua.xml", $vsep = '') {
		if(!$xml = $this->xml2array(file_get_contents($xmlfile))) return array();
		$ua = strtolower($this->UA);
		$data = array();
		foreach ($xml as $atts) {
			foreach($atts['elements'] as $xml) {
				if(isset($xml['attributes']['lookahead']) && intval($xml['attributes']['lookahead'])) $lookahead = intval($xml['attributes']['lookahead']); else $lookahead = 5; //Number of chars to search
				if(isset($xml['attributes']['lookback'])) $lookback = intval($xml['attributes']['lookback']); else $lookback = 1; //Disable or enable lookback
				$xml['name'] = strtolower($xml['name']);
				if(!isset($data[$xml['name']]) && !isset($data['bot'])) {
					if($st = stripos($ua, $xml['attributes']['search'])) {
						$data[$xml['name']] = isset($xml['attributes']['name'])? $xml['attributes']['name'] : $xml['attributes']['search'];
						if(!isset($xml['elements'])) {
							$vsearch = isset($xml['attributes']['vsearch'])? $xml['attributes']['vsearch'] : $xml['attributes']['search'];
							if($stv = stripos($ua, $vsearch)) {
								$data[$xml['name']] .= $vsep . preg_replace("/[^\d]/", "", substr($ua, ($stv+(strlen($vsearch)*$lookback)), $lookahead));
							}
						} else {
							foreach($xml['elements'] as $el) {
								if(!isset($version) && $el['name']=="version") {
									if($stv = stripos($ua, $el['attributes']['search'])) {
										$version = preg_replace("/[^\d]/", "", substr($ua, ($stv+(strlen($el['attributes']['search'])*$lookback)), $lookahead));
										$data[$xml['name']] .= $vsep . isset($el['attributes']['name'])? $el['attributes']['name'] : $version;
									}
								}
							}
						}
					}
				}
			}
		}

		if($lang = $this->lang($ua)) $data['lang'] = $lang;
		return $data;
	}

}

?>
