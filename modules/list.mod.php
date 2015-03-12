<?php

/**
 * 模块：动态数据显示
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name list.mod.php
 * @version 1.2
 */

class ModuleObject extends MasterObject
{
	var $city;
	var $cityname;
	var $ProductLogic;
	var $PayLogic;
	var $MeLogic;
	var $OrderLogic;
	function ModuleObject( $config )
	{
		$this->MasterObject($config); 		Load::logic('product');
		$this->ProductLogic = new ProductLogic();
		Load::logic('pay');
		$this->PayLogic = new PayLogic();
		Load::logic('me');
		$this->MeLogic = new MeLogic();
		Load::logic('order');
		$this->OrderLogic = new OrderLogic();
		$this->ID = ( int )($this->Post['id'] ? $this->Post['id'] : $this->Get['id']);
		$this->CacheConfig = ConfigHandler::get('cache'); 		$this->ShowConfig = ConfigHandler::get('show'); 		$runCode = Load::moduleCode($this, $this->Code);
		$this->$runCode();
	}
	function Main()
	{
		header('Location: '.rewrite('?mod=list&code=ask'));
	}
	function Ask()
	{
		$this->Title = __('在线问答');
		$action = '?mod=list&code=doquestion';
		include ($this->TemplateHandler->Template("ask"));
	}
	function Doquestion()
	{
		$question = post('question', 'txt');
		if ( MEMBER_ID < 1 ) $this->Messager(__('您必须先登录才能发表您的提问！'));
		if ( $question == '' ) $this->Messager(__('问题不可以为空哦！'));
		if ( $a = filter($question) ) $this->Messager($a);
		$ary = array(
			userid => MEMBER_ID, username => MEMBER_NAME, content => $question, time => time()
		);
		$this->DatabaseHandler->SetTable(TABLE_PREFIX . 'tttuangou_question');
		$result = $this->DatabaseHandler->Insert($ary);
		$ary['time'] = date('Y-m-d H:i:s', $ary['time']);
		notify(MEMBER_ID, 'list.ask.new', $ary);
		$this->Messager(__("提问成功，请等待管理员的回复！"), "?mod=list&code=ask");
		exit();
	}
	function Business()
	{ 		$this->Title = __('商务合作');
		$action = '?mod=index&code=doteamwork';
		include ($this->TemplateHandler->Template('business'));
	}
	function Doteamwork()
	{
		$this->__filter_post('name,phone,elsecontat,content');
		if ( $this->Post['name'] == '' || $this->Post['phone'] == '' || $this->Post['content'] == '' ) $this->Messager("缺少必要参数，请正确填写！");
		$ary = array(
			'name' => $this->Post['name'],
			'phone' => $this->Post['phone'],
			'elsecontat' => $this->Post['elsecontat'],
			'content' => $this->Post['content'],
			'time' => time(),
			'type' => 2,
			'readed' => 0
		);
		$this->MeLogic->UserMsg($ary);
		$this->Messager(__("我们已经记录下您的合作信息，我们将尽快给您回复！"), "?mod=list&code=business");
	}
	function Feedback()
	{ 		$this->Title = __('意见反馈');
		$action = '?mod=index&code=dofeedback';
		include ($this->TemplateHandler->Template('feedback'));
	}
	function Dofeedback()
	{
		$this->__filter_post('name,phone,elsecontat,content');
		if ( $this->Post['name'] == '' || $this->Post['phone'] == '' || $this->Post['content'] == '' ) $this->Messager("缺少必要参数，请正确填写！");
		$ary = array(
			'name' => $this->Post['name'],
			'phone' => $this->Post['phone'],
			'elsecontat' => $this->Post['elsecontat'],
			'content' => $this->Post['content'],
			'time' => time(),
			'type' => 1,
			'readed' => 0
		);
		$this->MeLogic->UserMsg($ary);
		$this->Messager(__("我们已经记录下您的反馈信息，感谢您对本站的支持！"), "?mod=list&code=feedback");
	}
	private function __filter_post($fields)
	{
		$list = explode(',', $fields);
		foreach ($list as $i => $fid)
		{
			$moyoCNT = &$this->Post[$fid];
			$moyoAFS = filter($moyoCNT);
			$moyoAFS && $this->Messager($moyoAFS);
		}
	}
	function Deals()
	{
		$this->Title = __('历史' . TUANGOU_STR);
		$product = logic('product')->GetList(logic('misc')->City('id'), PRO_ACV_No);
		include ($this->TemplateHandler->Template('deals'));
	}


   
	function Newdeals()
	{
		$this->Title = __('精彩' . TUANGOU_STR);
		$product = logic('product')->GetList(logic('misc')->City('id'), PRO_ACV_Yes);
		include ($this->TemplateHandler->Template('newdeals'));
	}


	function Sendemail()
	{
		extract($this->Post);
		if ( ! check_email($email) ) $this->Messager(__("邮箱地址有误！"));
		if ( isset($del) )
		{
			$this->MeLogic->mail($email, $city, 0);
		}
		else
		{
			$this->MeLogic->mail($email, $city, 1);
		}
		$this->Messager(__("操作成功！"), "?");
	}
	function Invite()
	{
		$this->Title = __('邀请有奖');
		if ( MEMBER_ID < 1 )
		{
			$this->Messager(__("请您先注册或登录！"), '?mod=account&code=login');
		}
		$finder = $this->MeLogic->finderList(user()->get('id'));
		include ($this->TemplateHandler->Template("invite"));
	}

	function Ckticket()
	{
		$number = get('number', 'number');
		$this->Title = __('消费券查询');
		$action = '?mod=list&code=dockticket';
		$sellerid = logic('seller')->U2SID(user()->get('id'));
		$seller_info = logic('seller')->GetOne(null,MEMBER_ID);
		$money = $seller_info['money'];
		$total_money = $seller_info['total_money'];
		$account_money = $seller_info['account_money'];
		$forbid_money = $seller_info['forbid_money'];
		include ($this->TemplateHandler->Template("tttuangou_ckticket"));
	}
	function Dockticket()
	{
		$number = get('number', 'number');
		is_numeric($number) || exit('<font color="red">编号不能为空或者包含其他字符！</font>');
		$do = get('do');
		if ( $do == 'check' )
		{
			$this->coupon_check($number);
		}
		elseif ( $do == 'getname' )
		{
			$this->coupon_getname($number);
		}
		else
		{
			$this->coupon_used($number, get('password'), get('morecoupon'));
		}
	}

	
	private function coupon_check($number)
	{
		$ticket = logic('coupon')->TicketGet($number);
		if ($ticket)
		{
			if ( $ticket['status'] == TICK_STA_Unused )
			{
				$msg = '<font color="green">该' . TUANGOU_STR . '券可以使用</font>';
				if($ticket['morecoupons']){
					$msg .= '|||<table><tr><td colspan="2"><font color="blue">相关团购券(勾选则一同消费)</font></td></tr>';
					$msg .= '<tr><td>券号</td><td>密码</td></tr>';
					foreach($ticket['morecoupons'] as $k => $v){
						$msg .= '<tr><td><label><input type="checkbox" name="morecoupons" value="'.$v.'">&nbsp;'.$v.'</label></td><td>***</td></tr>';
					}
					$msg .= '</table><script> 
									$("input[name=morecoupons]").click(function(){ 
										var tips_product_len = $("input:checkbox:checked").length+1; 
										$("#tips_product_len").html("<em>您总共选择了</em>"+tips_product_len+"<em>张团购券（含查询的券）</em>");
									}) 
								</script> ';
				}
			}
			elseif ( $ticket['status'] == TICK_STA_Used )
			{
				$msg = '<font color="blue">该' . TUANGOU_STR . '券已经被使用，消费时间：' . $ticket['usetime'] . '</font>';
			}
			elseif ( $ticket['status'] == TICK_STA_Invalid )
			{
				$msg = '<font color="red">该' . TUANGOU_STR . '券已失效！</font>';
			}
			else
			{
				$msg = '<font color="red">该' . TUANGOU_STR . '券已过期！</font>';
			}
			exit($msg);
		}
		else
		{
			exit('<font color="red">该' . TUANGOU_STR . '券不存在！</font>');
		}
	}
	
	private function coupon_getname($number)
	{
		$product = logic('coupon')->ProductGet($number);
		if ($product)
		{
			$as = '<br/>';
			if (isset($product['coupon']['attrs']) && $product['coupon']['attrs'])
			{
				foreach ($product['coupon']['attrs']['dsp'] as $attr)
				{
					$as .= '<em>('.$attr['name'].')</em>';
				}
			}
			exit($product['flag'].' X <font color="red"><b>'.$product['coupon']['mutis'].'</b></font> 份'.$as);
		}
		else
		{
			exit('<font color="red">没有找到该产品！</font>');
		}
	}
	
	private function coupon_used($number, $password, $morecoupon = '')
	{
		$result = logic('coupon')->MakeUsed($number, $password, 0, $morecoupon);
		if ($result['error'])
		{
			switch ($result['errcode'])
			{
				case 'not-found' :
					exit('<font color="red">该' . TUANGOU_STR . '券不存在！</font>');
					break;
				case 'be-used' :
					exit('<font color="blue">该' . TUANGOU_STR . '券已经被使用，消费时间：' . $result['coupon']['usetime'] . '</font>');
					break;
				case 'be-overdue' :
					exit('<font color="red">该' . TUANGOU_STR . '券已过期！</font>');
					break;
				case 'be-invalid' :
					exit('<font color="red">该' . TUANGOU_STR . '券已失效！</font>');
					break;
				case 'access-denied' :
					exit('<font color="red">此' . TUANGOU_STR . '券不属于您的产品！</font>');
					break;
				case 'password-wrong' :
					exit('<font color="red">该' . TUANGOU_STR . '券的密码输入错误！</font>');
					break;
			}
		}
		else
		{
			if(isset($result['coupon']['number']) && isset($result['coupon']['password']) && $number == $result['coupon']['number']) {
				exit('<font color="green">已成功消费 </font><font color="red">1</font><font color="green"> 张' . TUANGOU_STR . '券：'.$number.'</font>|||success');
			} else {
				exit('<font color="green">已成功消费 </font><font color="red">'.count($result['coupon']).'</font><font color="green"> 张' . TUANGOU_STR . '券：'.implode(',',$result['coupon']).'</font>|||success');
			}
		}
	}

	
	
	public function couponcheck400()
	{
		$number = get('num', 'number');
		is_numeric($number) || $this->print_result1(0);
		$ticket = logic('coupon')->TicketGet($number);
		if ($ticket)
		{
			if ( $ticket['status'] == TICK_STA_Unused )
			{
				$product = logic('coupon')->ProductGet($number);
				$this->print_result1(3, $product['productid'], $product['flag'], $product['nowprice']);
			}
			elseif ( $ticket['status'] == TICK_STA_Used )
			{
				$this->print_result1(1);
			}
			else
			{
				$this->print_result1(2);
			}
		}
		else
		{
			$this->print_result1(0);
		}
	}

	
	public function couponuse400()
	{
		$number = get('num', 'number');
		$password = get('secret', 'number');
		if(empty($number) || empty($password)) {
			$this->print_result2();
		}
		
		$product = logic('coupon')->ProductGet($number);
		if ($product && $product['coupon']['status'] == TICK_STA_Unused)
		{

			if ($product['coupon']['password'] != $password)
			{
				$this->print_result2();
			}
			else
			{
				$usetime = date('Y-m-d H:i:s', time());
								dbc(DBCMax)->update('ticket')->data(array('status' => TICK_STA_Used, 'usetime' => $usetime))->where(array('ticketid' => $product['coupon']['ticketid']))->done();
								logic('notify')->Call($product['coupon']['uid'], 'logic.coupon.Used', array(
					'productflag' => $product['flag'],
					'number' => $product['coupon']['number'],
					'time' => $usetime
				));
				zlog('coupon')->used($product['coupon']);
								$product['coupon']['flag'] = $product['flag'];
								logic('rebate')->Add_Rebate_For_Ticket($product);
								$this->print_result2('true', $product['flag'], $product['nowprice']);
			}

		}
		else
		{
			$this->print_result2();
		}
	}

	private function print_result1($result = 0, $id = null, $product = null, $price = null){
		if(true === ENC_IS_GBK) {
			$product = ENC_G2U($product);
		}
		$result = <<<XMLFILE
<?xml version="1.0" encoding="UTF-8" ?>
<coupon>
	<result>{$result}</result>
	<id>{$id}</id>
	<product>{$product}</product>
	<price>{$price}</price>
</coupon>
XMLFILE;
		header('Content-Type: text/xml');
		echo $result;
		exit(0);
	}

	private function print_result2($result = 'false', $product = null, $price = null){
		if(true === ENC_IS_GBK) {
			$product = ENC_G2U($product);
		}
		$result = <<<XMLFILE
<?xml version="1.0" encoding="UTF-8" ?>
<coupon>
	<result>{$result}</result>
	<product>{$product}</product>
	<price>{$price}</price>
</coupon>
XMLFILE;
		header('Content-Type: text/xml');
		echo $result;
		exit(0);
	}

}

?>