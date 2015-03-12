<?php

/**
 * 支付方式：支付宝移动快捷支付
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package payment
 * @name alipaymobile.php
 * @version 1.0
 */

class alipaymobilePaymentDriver extends PaymentDriver
{
    
    public function inner_disabled()
    {
        return WEB_BASE_ENV_DFS::$APPNAME == 'index';
    }
    
    private $Gateway_com = 'http://notify.alipay.com/trade/notify_query.do?';
    
    public function CreateLink($payment, $parameter)
    {
                $parameter['name'] = preg_replace('/\&[a-z]{2,4}\;/i', '', $parameter['name']);
        $parameter['detail'] = str_replace(array('"',"'",'\\','&',"\r","\n",'{','}'), '', $parameter['detail']);
                $post = array(
                        'service'           => 'mobile.securitypay.pay',
            'payment_type'      => '1',
                        'seller_id'		=> $payment['config']['account'],
            'partner'			=> $payment['config']['partner'],
            'return_url'		=> $parameter['notify_url'],
            'notify_url'		=> $parameter['notify_url'],
            '_input_charset'	=> 'utf-8',
            'show_url'			=> $parameter['product_url'],
                        'out_trade_no'		=> $parameter['sign'],
            'subject'			=> $parameter['name'],
            'body'				=> $parameter['detail'],
            'total_fee'			=> $parameter['price'],
            'it_b_pay' => '30m'
        );
        return $this->rsa_sign($payment, $post);
    }

    
    public function StreamVerify($order, $stream)
    {
        strstr($stream, '\\"') && $stream = stripslashes($stream);
        preg_match_all('/([a-z]+)={(.*?)};?/i', $stream, $results);
        if ($results)
        {
            if ($results[1])
            {
                $args = array();
                foreach ($results[1] as $i => $result_key)
                {
                    $args[$result_key] = $results[2][$i];
                }
                if ((int)$args['resultStatus'] == 9000)
                {
                    preg_match_all('/([a-z_]+)="(.*?)"&?/i', $args['result'], $params);
                    if ($params)
                    {
                        if ($params[1])
                        {
                            $gets = array();
                            foreach ($params[1] as $i => $param_key)
                            {
                                $gets[$param_key] = $params[2][$i];
                            }
                            $sign = $gets['sign'];
                            $verify = $this->rsa_verify($gets, $sign, array('sign', 'sign_type'));
                            if ($verify)
                            {
                                return 'TRADE_OK';
                            }
                        }
                    }
                }
            }
        }
        return 'TRADE_ERROR';
    }
    
    public function CreateConfirmLink($payment, $order)
    {
        return '?mod=buy&code=tradeconfirm&id='.$order['orderid'];
    }
    
    public function CallbackVerify($payment)
    {
        return $this->__Trade_Status($this->__Notify_Verify($payment));
    }
    
    public function GetTradeData()
    {
        $src = 'POST';
        $trade = array();
        $trade['sign'] = logic('safe')->Vars($src, 'out_trade_no', 'number');
        $trade['trade_no'] = logic('safe')->Vars($src, 'trade_no', 'number');
        $trade['price'] = logic('safe')->Vars($src, 'total_fee', 'float');
        $trade['money'] = $trade['price'];
        $trade['status'] = $this->__Trade_Status(logic('safe')->Vars($src, 'trade_status', 'txt'));
		$order = logic('order')->GetOne($trade['sign']);
		$trade['uid'] = $order['userid'];
        return $trade;
    }
    
    public function StatusProcesser($status)
    {
        if ($status != 'VERIFY_FAILED')
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
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
    }

    
    private function __Trade_Status($trade_status)
    {
        return ($trade_status == 'TRADE_SUCCESS') ? 'TRADE_FINISHED' : $trade_status;
    }

    
    private function __Notify_Verify($payment)
    {
        $url = $this->Gateway_com
            .'partner='.$payment['config']['partner']
            .'&notify_id='.post('notify_id', 'txt');

        $result = $this->__Verify($url);

        $parameter = $this->__para_filter($_POST, array('mod', 'pid'));
        $parameter = $this->__arg_sort($parameter);
        $sign_success = $this->rsa_verify($parameter, post('sign', 'txt'), array(), 'web');

        if (preg_match('/true$/i', $result) && $sign_success)
        {
            return post('trade_status', 'txt');
        }
        else
        {
            return 'VERIFY_FAILED';
        }
    }

    
    private function __Verify($url, $time_out = '6')
    {
        $urlArr     = parse_url($url);
        $errNo      = '';
        $errStr     = '';
        if($urlArr['scheme'] == 'https')
        {
            $transPorts = 'ssl://';
            $urlArr['port'] = '443';
        }
        else
        {
            $transPorts = '';
            $urlArr['port'] = '80';
        }
        $fp = msockopen($transPorts . $urlArr['host'], $urlArr['port'], $errNo, $errStr, $time_out);
        if(!$fp)
        {
            zlog('error')->found('error.msockopen');
            die("ERROR: $errNo - $errStr<br />\n");
        }
        else
        {
            fputs($fp, "POST ".$urlArr["path"]." HTTP/1.1\r\n");
            fputs($fp, "Host: ".$urlArr["host"]."\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: ".strlen($urlArr["query"])."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $urlArr["query"] . "\r\n\r\n");
            while(!feof($fp))
            {
                $info[]=@fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",",$info);
            return $info;
        }
    }

    
    private function __SrvGET($url, $time_out = '6')
    {
        $urlArr     = parse_url($url);
        $errNo      = '';
        $errStr     = '';
        $transPorts = '';
        if($urlArr['scheme'] == 'https')
        {
            $transPorts = 'ssl://';
            $urlArr['port'] = '443';
        }
        else
        {
            $transPorts = '';
            $urlArr['port'] = '80';
        }
        $fp = msockopen($transPorts . $urlArr['host'], $urlArr['port'], $errNo, $errStr, $time_out);
        if(!$fp)
        {
            zlog('error')->found('error.msockopen');
            die("ERROR: $errNo - $errStr<br />\n");
        }
        else
        {
            fputs($fp, "GET ".$urlArr["path"]."?".$urlArr["query"]." HTTP/1.1\r\n");
            fputs($fp, "Host: ".$urlArr["host"]."\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            while(!feof($fp))
            {
                $info[]=@fgets($fp, 1024);
            }
            fclose($fp);
            $info = implode(",",$info);
            return $info;
        }
    }

    
    public function rsa_sign($payment, $parameter)
    {
        $parameter = $this->__para_filter($parameter, array('mod', 'pid'));
        $parameter = $this->__arg_sort($parameter);
        $parameter['subject'] || $parameter['subject'] = '-';
        $parameter['body'] || $parameter['body'] = '-';
        $string = $this->__create_linkstring_urlencode($parameter);
        $parameter_sign = $parameter;
        $parameter_sign['subject'] = ENC_IS_GBK ? ENC_G2U($parameter['subject']) : $parameter['subject'];
        $parameter_sign['body'] = ENC_IS_GBK ? ENC_G2U($parameter['body']) : $parameter['body'];
        $string_sign = $this->__create_linkstring_urlencode($parameter_sign);
                $rsa_handler = openssl_get_privatekey($payment['config']['web_pri_key']);
        openssl_sign($string_sign, $rsa_sign, $rsa_handler);
        openssl_free_key($rsa_handler);
        $rsa_sign = base64_encode($rsa_sign);
                        $string .= '&sign="'.urlencode($rsa_sign).'"&sign_type="RSA"';
                return $string;
    }

    
    private function rsa_verify($gets, $sign, $ignores = array(), $from = 'client')
    {
        foreach ($ignores as $ignore_key)
        {
            unset($gets[$ignore_key]);
        }
                if (ENC_IS_GBK)
        {
            $loops = array('subject', 'body');
            foreach ($loops as $loopKey)
            {
                if (isset($gets[$loopKey]) && $gets[$loopKey])
                {
                    $charset = $this->mbcharset($gets[$loopKey]);
                    if ($charset != 'utf8')
                    {
                        $gets[$loopKey] = ENC_G2U($gets[$loopKey]);
                    }
                }
            }
        }
        $string = '';
        foreach ($gets as $k => $v)
        {
            if ($from == 'client')
            {
                $string .= $k.'="'.$v.'"&';
            }
            else
            {
                $string .= $k.'='.$v.'&';
            }
        }
        $string = substr($string, 0, -1);
        $payment = logic('pay')->GetOne('alipaymobile');
        $rsa_handler = openssl_get_publickey($payment['config']['ali_pub_key']);
        $result = (bool)openssl_verify($string, base64_decode($sign), $rsa_handler);
        openssl_free_key($rsa_handler);
        return $result;
    }

    
    private function mbcharset($content)
    {
        $detect_order = array('ASCII', 'UTF-8', 'GBK');
        $charset = mb_detect_encoding($content, $detect_order);
        $charsetMap = array(
            'ASCII' => 'ascii',
            'UTF-8' => 'utf8',
            'CP936' => 'gbk'
        );
        return isset($charsetMap[$charset]) ? $charsetMap[$charset] : $charset;
    }
    private function __create_linkstring($array)
    {
        $arg  = '';
        foreach ($array as $key => $val)
        {
            $arg .= $key.'='.$val.'&';
        }
        $arg = substr($arg, 0, count($arg)-2);
        return $arg;
    }
    private function __create_linkstring_urlencode($array, $in = array('notify_url', 'return_url', 'show_url'))
    {
        $arg  = '';
        foreach ($array as $key => $val)
        {
            if (in_array($key, $in))
            {
                $arg .= $key.'="'.urlencode($val).'"&';
            }
            else
            {
                $arg .= $key.'="'.$val.'"&';
            }
        }
        $arg = substr($arg, 0, count($arg)-2);
        return $arg;
    }
    private function __arg_sort($array)
    {
        ksort($array);
        reset($array);
        return $array;
    }
    private function __para_filter($parameter, $excludes = array())
    {
        $ignores = array(
            'sign' => 1,
            'sign_type' => 1
        );
        foreach ($excludes as $exclude)
        {
            $ignores[$exclude] = 1;
        }
        $para = array();
        foreach ($parameter as $key => $val)
        {
            if(isset($ignores[$key]) || $val == '')
            {
                continue;
            }
            else
            {
                $para[$key] = $val;
            }
        }
        return $para;
    }
}

?>