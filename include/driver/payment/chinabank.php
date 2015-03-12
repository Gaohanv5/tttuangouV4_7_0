<?php

/**
 * 支付方式：网银在线
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name chinabank.php
 * @version 1.0
 */

class chinabankPaymentDriver extends PaymentDriver
{
	
	public function CreateLink($payment, $parameter)
	{
				$parameter['name'] = preg_replace('/\&[a-z]{2,4}\;/i', '', $parameter['name']);
		$parameter['detail'] = str_replace(array('"',"'",'\\','&'), '', $parameter['detail']);

				$data_vid           = $payment['config']['account'];
		$data_orderid       = $parameter['sign'];
		$data_vamount       = $parameter['price'];
		$data_vmoneytype    = 'CNY';
		$data_vpaykey       = $payment['config']['key'];
		$data_vreturnurl    = $parameter['notify_url'];

		$MD5KEY =$data_vamount.$data_vmoneytype.$data_orderid.$data_vid.$data_vreturnurl.$data_vpaykey;
		$MD5KEY = strtoupper(md5($MD5KEY));

		$def_url = '<br/><b>点击下面的 “网银支付” 后可以在新打开窗口中选择您的银行卡类型进行支付！</b>';
		$def_url .= '<form id="pay_submit" name="chinabanksubmit" action="https://pay3.chinabank.com.cn/PayGate" method=post target="_blank">';
		$def_url .= "<input type=HIDDEN name='v_mid' value='".$data_vid."'>";
		$def_url .= "<input type=HIDDEN name='v_oid' value='".$data_orderid."'>";
		$def_url .= "<input type=HIDDEN name='v_amount' value='".$data_vamount."'>";
		$def_url .= "<input type=HIDDEN name='v_moneytype'  value='".$data_vmoneytype."'>";
		$def_url .= "<input type=HIDDEN name='v_url'  value='".$data_vreturnurl."'>";
		$def_url .= "<input type=HIDDEN name='v_md5info' value='".$MD5KEY."'>";
		$def_url .= "<input type=HIDDEN name='remark1' value=''>";
		$def_url .= '<input type=submit value="网银支付" onclick="javascript:$.hook.call(\'pay.button.click\');">';
		$def_url .= "</form>";

		return $def_url;
	}
	
	public function CreateConfirmLink($payment, $order)
	{
		return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
	}
	
	public function CallbackVerify($payment)
	{
		if ($this->__Is_Nofity())
		{
						sleep(3);
		}
				$v_oid          = trim($_POST['v_oid']);
		$v_pmode        = trim($_POST['v_pmode']);
		$v_pstatus      = trim($_POST['v_pstatus']);
		$v_pstring      = trim($_POST['v_pstring']);
		$v_amount       = trim($_POST['v_amount']);
		$v_moneytype    = trim($_POST['v_moneytype']);
		$remark1        = trim($_POST['remark1' ]);
		$remark2        = trim($_POST['remark2' ]);
		$v_md5str       = trim($_POST['v_md5str' ]);

		$key            = $payment['config']['key'];

		$md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));

		if ($v_md5str != $md5string)
		{
			return 'VERIFY_FAILED';
		}
		if ($v_pstatus != '20')
		{
			return 'VERIFY_FAILED';
		}
		return 'TRADE_FINISHED';
	}
	
	public function GetTradeData()
	{
		$src = 'POST';
		$trade = array();
		$trade['sign'] = logic('safe')->Vars($src, 'v_oid', 'number');
		$trade['trade_no'] = logic('safe')->Vars($src, 'v_oid', 'number');
		$trade['price'] = logic('safe')->Vars($src, 'v_amount', 'float');
		$trade['money'] = $trade['price'];
		$trade['status'] = 'TRADE_FINISHED';
		return $trade;
	}
	
	public function StatusProcesser($status)
	{
		if (!$this->__Is_Nofity())
		{
			return false;
		}
		if ($status != 'VERIFY_FAILED')
		{
			echo 'ok';
		}
		else
		{
			echo 'error';
		}
		return true;
	}
	
	public function GoodSender($payment, $express, $sign, $type)
	{
		if ($type == 'ticket')
		{
			logic('callback')->Bridge($sign)->Processed($sign, 'TRADE_FINISHED');
		}
		else
		{
			logic('callback')->Bridge($sign)->Processed($sign, 'WAIT_BUYER_CONFIRM_GOODS');
		}
		return;
	}
	
	private function __Is_Nofity()
	{
		return get('pid') == 'chinabank';
	}
}

?>