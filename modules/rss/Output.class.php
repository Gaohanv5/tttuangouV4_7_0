<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name Output.class.php
 * @date 2014-09-01 17:24:23
 */
 


class Output
{
	static private $tagSon = array();

	static public function SetTagSon($parent, $son) {
		self::$tagSon[$parent] = $son;
	}

	static private function Error($error=0)
	{
		return array( 'error' => intval($error), );
	}

	static public function ArrayToXml($array, $level=0, $topTagName='result', $topTagAttr = '')
	{
		if ($topTagName) {
			$xml = str_repeat("\t",$level) . "<$topTagName"."$topTagAttr>\n";
			$level++;
		}

		foreach ($array as $key=>$value) {
			if( is_numeric($key) ){
				$key = self::GetSubTagName($topTagName);
			}

			if($value===false) $value='false';
			if($value===true) $value='true';

			if (is_array($value)) {
				$xml .= self::ArrayToXml($value,$level,$key);
			} else {
				if (thtmlspecialchars($value) != $value || strstr($key, '<[cd]>')) {
                    $key = str_replace('<[cd]>', '', $key);
					$xml .= str_repeat("\t",$level)
						."<$key><![CDATA[$value]]></$key>\n";
				} else {
					$xml .= str_repeat("\t",$level).
						"<$key>$value</$key>\n";
				}
			}
		}

		if ($topTagName) {
			$xml .= str_repeat("\t",($level-1)) . "</$topTagName>\n";
		}
		return $xml;
	}

	static public function GetSubTagName($tagName)
	{
		if ( self::$tagSon[$tagName] ) {
			return self::$tagSon[$tagName];
		}
		if ( preg_match( '/ies$/', $tagName ) ) 			return preg_replace( '/(ies)$/', 'y', $tagName );

		if ( preg_match( '/ses$/', $tagName ) )  			return preg_replace( '/(es)$/', '', $tagName );

		if ( preg_match( '/s$/', $tagName ) ) 			return preg_replace( '/(s)$/', '', $tagName );

		if ( preg_match( '/urlset$/', $tagName ) ) 			return 'url';

		return 'item';
	}

	static public function Out($data=null, $error=0)
	{
		$ajax = isset($_SERVER['HTTP_AJAX'])
			? strtoupper($_SERVER['HTTP_AJAX']) : 'JSON';
		switch($ajax)
		{
			case 'XML':
				self::Xml($data, $error);
			case 'FLAG':
				$flag = $error===0 ? '+' : '-';
				self::Flag($data, $flag);
			case 'JSON':
				self::Json($data, $error);
			default:
				self::Json($data, $error);
		}
	}

	static public function Json($data=null, $error=0)
	{
		$result = self::error( $error );
		if ( null !== $data )
		{
			$result['data'] = $data;
		}
		die( json_encode($result) );
	}

	static public function Xml($data=null, $error=0, $encode="utf-8")
	{
		$result = self::error( $error );
		if ( null !== $data )
		{
			$result['data'] = $data;
		}

		$xml = "<?xml version=\"1.0\" encoding=\"{$encode}\"?>\n";
		$xml .= self::ArrayToXml( $result, 0, 'result' );
		if (strtolower($encode)!=='utf-8') {
			$xml = mb_convert_encoding($xml, $encode, 'UTF-8');
		}
		die( $xml );
	}

	static public function XmlCustom($data, $ptag=null, $encode="utf-8") {
		$xml = "<?xml version=\"1.0\" encoding=\"{$encode}\"?>\n";
		$xml .= self::ArrayToXml( $data, 0, $ptag);
		if (strtolower($encode)!=='utf-8') {
			$xml = mb_convert_encoding($xml, $encode, 'UTF-8');
		}
		die( $xml );
	}

	static public function XmlBaidu($data=null, $error=0, $topTagAttr = '')
	{
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= self::ArrayToXml( $data, 0, 'urlset', $topTagAttr );
		die( $xml );
	}

	static public function Flag($string=null, $flag='+')
	{
		$flag = substr( $flag, 0, 1 );
		die( $flag . $data );
	}
}
?>
