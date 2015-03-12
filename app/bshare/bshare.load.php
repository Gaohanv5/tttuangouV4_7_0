<?php

/**
 * 应用：bShare 分享
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package app
 * @name bshare.load.php
 * @version 1.0
 */

class bShareAPP
{
	private $entryCS = 0;
	public function load($page, $data)
	{
		$method = 'load_'.$page;
		if (method_exists($this, $method))
		{
			$this->$method($data);
		}
	}
	private function load_product_detail($data)
	{
		$p = $data['product'];
		$url = ini('settings.site_url').'/?view='.$p['id'];
		$uid = user()->get('id');
		if ($uid > 0)
		{
			$url .= '&u='.$uid;
		}
		$url = rewrite($url);
		$this->getScript();
		$this->getLinks($uid,$p['id']);
		$this->getEntry($url, $p['name'], $p['intro']);
	}
	private function load_prize_view($data)
	{
		$p = $data['product'];
		$url = ini('settings.site_url').'/?view='.$p['id'];
		$uid = user()->get('id');
		if ($uid > 0)
		{
			$url .= '&u='.$uid;
		}
		$url = rewrite($url);
		$this->getScript();
		$this->getLinks($uid,$p['id']);
		$this->getEntry($url, $p['name'], $p['intro']);
	}
	private function getPlatforms($implodeChar = false)
	{
		$specialDf = array(
			'link' => 'qzone',
			'tsina' => 'sinaminiblog',
			'bai' => 'sohubai',
			'yahoo' => 'byahoo',
			'google' => 'bgoogle'
		);
		$shares = ini('share');
		$rArray = array();
		$rString = '';
		foreach ($shares as $flag => $one)
		{
			if ($flag == '~@bshare' || $one['display'] == 'no')
			{
				continue;
			}
			if (isset($specialDf[$flag]))
			{
				$flag = $specialDf[$flag];
			}
			if ($implodeChar)
			{
				$rString .= $flag.$implodeChar;
			}
			else
			{
				$rArray[$flag] = $one;
			}
		}
		if (!$implodeChar && ini('share.~@bshare.more'))
		{
			$rArray['more'] = array('name' => '更多');
		}
		return $implodeChar ? substr($rString, 0, -1) : $rArray;
	}
	private function getLinks($uid=0,$pid=0)
	{
		$html = '<span class="share-tip">分享到</span>';
		$html .= '<ul class="share-list">';
		$plats = $this->getPlatforms();
		$bshare = ini('share.~@bshare');
		foreach ($plats as $flag => $one)
		{
			$img = '<img src="http://static.bshare.cn/frame/images/logos/s4/'.$flag.'.gif" style="height:16px; width:16px; vertical-align:middle;"/>';
			$link = '';
			if ($bshare['sn'])
			{
				$link = $one['name'];
			}
			$js = 'bShare.share(event, \''.$flag.'\', '.$this->entryCS.')';
			if ($flag == 'more')
			{
				$js = 'bShare.more(event)';
			}
			if($uid > 0 && $pid > 0){
				$js = "sharescore('".$pid."','".$uid."','".$one['name']."');" . $js;
			}
			$html .= '<li><a href="javascript:;" onclick="javascript:'.$js.';return false;" title="分享到'.$one['name'].'">'.$img.$link.'</a></li>';
		}
		
		if ($bshare['ssc'])
		{
			$html .= '<div style="float:left;width:41px;background:transparent url(http://static.bshare.cn/frame/images/counter_box_18.gif) no-repeat;height:18px;margin:0 0 0 5px;text-align:center;font:bold 11px Arial;"><span class="BSHARE_COUNT" style="position:relative;line-height:18px;font-size:11px;top:0px;left:0px;color:rgb(51,51,51);float:none;"></span></div>';
		}
		$html .= '</ul>';
		echo $html;
	}
	private function getScript()
	{
		$bshare = ini('share.~@bshare');
		echo '<script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#uuid='.$bshare['uuid'].'&style=-1"></script>';
	}
	private function getEntry($url, $title, $summary)
	{
		$title = $this->jsFilter($title);
		$summary = $this->jsFilter($summary);
		$js  = '<script type="text/javascript" charset="utf-8">';
		$js .= 'bShare.addEntry({';
		$js .= 'title:"'.$title.'",';
		$js .= 'url:"'.$url.'",';
		$js .= 'summary:"'.$summary.'"';
		$js .= '});';
		$js .= '</script>';
		echo $js;
		$this->entryCS ++;
	}
	private function jsFilter($c)
	{
		$c = str_replace(array("\n", "\r", '"', "'", '\\'), '', $c);
		$c = preg_replace('/\<.*?\>/i', '', $c);
		return $c;
	}
}

?>