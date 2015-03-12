<?php
/**
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package php
 * @name dsp.now.updating.php
 * @date 2013-08-12 15:32:54
 */
 


$lng = array();

$lng['charset'] = $this->config['charset'];
$lng['title'] = '系统正在维护';
$lng['content'] = '我们会很快回来，敬请期待。';

?><!doctype html>
<html lang="en">
<head>
<meta charset="<?=$lng['charset']?>">
<title><?=$lng['title']?></title>
<style type="text/css">
.box {
	width: 500px;
	margin: 0 auto;
	margin-top: 100px;
	border: 2px dashed #ccc;
	padding: 30px;
}
.box h2 {
	border-bottom: 1px solid #ccc;
	padding-bottom: 10px;
}
</style>
</head>
<body>
	<div class="box">
		<h2><?=$lng['title']?></h2>
		<p><?=$lng['content']?></p>
	</div>
</body>
</html>