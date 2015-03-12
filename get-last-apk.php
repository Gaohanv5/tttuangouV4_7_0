<?php
/**
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package php
 * @name get-last-apk.php
 * @date 2013-11-25 17:11:21
 */
 


$dir = './uploads/apks/release/';

if (is_dir($dir))
{
	$releases = array();
	$handler = opendir($dir);
	while (false != $file = readdir($handler))
	{
		if (preg_match('/^(\w+)\.(.*?)\-(\d{8})\.apk$/i', $file, $match))
		{
			$releases[$match[3]] = $match[0];
		}
	}
	closedir($handler);
	if ($releases)
	{
		ksort($releases);
		$last = end($releases);
		if ($last)
		{
			header('Location: '.$dir.$last);
			exit;
		}
	}
}

exit('APK.GET(LAST).ERROR');

?>