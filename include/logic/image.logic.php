<?php

/**
 * 图片处理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name image.logic.php
 * @version 1.0
 */

class ImageProcesserLogic
{
	
	public function thumb($image_source, $image_dest, $width, $height)
	{
		handler('image')->setSrcImg($image_source);
		handler('image')->setDstImg($image_dest);
		handler('image')->createImg($width, $height);
		return $image_dest;
	}
	
	public function water($image_source, $image_dest, $config)
	{
		if ($config['type'] == 'image')
		{
						handler('image')->setSrcImg($image_source);
			handler('image')->setDstImg($image_dest);
			handler('image')->setMaskImg($config['image']);
			handler('image')->setMaskPosition($config['position']);
			handler('image')->createImg(100);
		}
		elseif ($config['type'] == 'text')
		{
			if (ENC_IS_GBK)
			{
								$config['text'] = ENC_G2U($config['text']);
			}
						$config['text'] = mb_convert_encoding($config['text'], 'html-entities', 'UTF-8');
 			$r = array();
						$r[] = handler('image')->setSrcImg($image_source);
			$r[] = handler('image')->setDstImg($image_dest);
			$r[] = handler('image')->setMaskFont(ROOT_PATH.'static/images/watermark/'.$config['font']);
			$r[] = handler('image')->setMaskFontColor('#ffffff');
			$r[] = handler('image')->setMaskFontSize($config['fontsize'] ? $config['fontsize'] : 13);
			$r[] = handler('image')->setMaskWord($config['text']);
			$r[] = handler('image')->setMaskPosition($config['position']);
			$r[] = handler('image')->createImg(100);
		}
	}
}

?>