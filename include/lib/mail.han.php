<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name mail.han.php
 * @date 2014-09-01 17:24:22
 */
 


substr(PHP_OS,0,3)=="WIN"?define("NEW_LINE","\r\n"):define("NEW_LINE","\n");
class MailHandler
{
	var $SenderName;       
	var $SenderMail;    
	var $Subject;			
	var $Message;     
	var $Headers;	
	var $Recipient;  
	var $Html;  
	
	function MailHandler()
	{
		$this->SenderMail  = '';
		$this->SenderName    = '';
		$this->Html      = true;

		$this->Recipient = '';
		$this->Message   = '';
		$this->Subject   = '';
		$this->Headers   = array();
	}
	
	function SetSmtpHost($smtp)
	{
		Return ini_set("SMTP",$smtp);
	}

	
	function SetSendPort($port)
	{
		Return ini_set("smtp_port",$port);

	}

	
	function SetSendMailFrom($mail)
	{
		Return 	ini_set('sendmail_from',$mail);
;

	}


	
	function SetSenderName($sender)
	{
		$this->SenderName=$sender;
	}

	
	function SetSenderMail($mail)
	{
		$this->SenderMail=$mail;
	}


	
	function SetRecipient($recipient)
	{
		$this->Recipient = $recipient;
	}

	
	function SetUseHtml($html=true)
	{
		$this->Html = (bool)$html;
	}


	
	function SetSubject($subject)
	{
		$this->Subject = $subject;
	}



   
	function SetMessage($message)
	{
		$this->Message = $message;
	}

	
	function SetHeader($header)
	{
		$this->Headers[] = $header;
	}

	
	function GetHeader($key)
	{
		return (isset($this->Headers[$key]) ? $this->Headers[$key] : false);
	}

	
	function DoSend()
	{
		if(false == $this->Subject || false == $this->Subject)
		{
			return false;
		}

		$this->SetHeader("From: \"{$this->SenderName}\" <{$this->SenderMail}>");
		$to = (strlen($this->Recipient) ? $this->Recipient : $this->SenderName . ' <' . $this->SenderMail . '>');

		$result= @mail($to, $this->Subject, $this->Message, implode(NEW_LINE, $this->Headers)) ? true : false;
		$this->Headers="";
		Return $result;
	}

}


function send_mail($to,$subject,$message,$nickname='',$email='',$attachments=array(),$priority=3,$html=true,$smtp_config=array())
{
	if(!($nickname && $email)) {
		$sys_config = ConfigHandler::get();

		$nickname = $sys_config['site_name'];
		$email = $sys_config['site_admin_email'];
	}

	$smtp_config = $smtp_config ? $smtp_config : ConfigHandler::get('smtp');
	if($smtp_config['enable'])
	{
		if($nickname && $email) $smtp_config['email_from'] = "{$nickname} <{$email}>";

		return _send_mail_by_smtp($to,$subject,$message,$smtp_config,$html);

		
	}
	else
	{
		static $MailHandler=null;
		if(is_null($MailHandler)!=false)
		{
			$MailHandler=new MailHandler($email,$nickname,true);
		}
		if(is_array($attachments) and count($attachments)>=1)
		{

			$boundary="----_NextPart_".md5(uniqid(time()))."_000";
			$MailHandler->SetHeader('Content-Type: multipart/mixed;boundary="'.$boundary.'"');
			$body="--".$boundary."".NEW_LINE."";
			$body.="Content-Type: text/html; charset=\"GB2312\"".NEW_LINE."";
			$body.="Content-Transfer-Encoding: base64".NEW_LINE."".NEW_LINE."";
			$body.=chunk_split(base64_encode($message))."".NEW_LINE."";

			foreach($attachments as $attachment)
			{
				$body.="--".$boundary."".NEW_LINE."";
				$body.="Content-Type: application/octet-stream;".NEW_LINE."\t\tname=\"{$attachment['name']}\"".NEW_LINE."";
				$body.="Content-Transfer-Encoding: base64".NEW_LINE."";
				$body.="Content-Disposition: attachment;".NEW_LINE."\t\tFileName=\"{$attachment['name']}\"".NEW_LINE."".NEW_LINE."";
				$body.=chunk_split(base64_encode(file_get_contents($attachment['path'])))."".NEW_LINE."";;
			}
						$message=$body;
								}
		else
		{
			if(false!=$html)
			{
				$MailHandler->SetHeader('Content-Type: text/html; charset=gb2312');
			}
		}

		$MailHandler->SetSenderName($nickname);
		$MailHandler->SetSenderMail($email);
		$MailHandler->SetSendMailFrom($email);
		$MailHandler->SetUseHtml($html);
		$MailHandler->SetHeader("Return-Path: {$email}");
		$MailHandler->SetHeader("MIME-Version: 1.0");
		$MailHandler->SetHeader("X-Priority: $priority");
		$MailHandler->SetHeader("Sender: {$email}");
		$MailHandler->SetRecipient($to);
		$MailHandler->SetSubject($subject);
		$MailHandler->SetMessage($message);
		Return $MailHandler->doSend();
	}
}

function _send_mail_by_smtp($email_to,$email_subject,$email_message,$smtp_config='',$html=true) {
	$sys_config = ConfigHandler::get();
	$smtp_config = $smtp_config ? $smtp_config : (ConfigHandler::get('smtp'));

	$mail['from'] = $smtp_config['mail'];
	$mail['server'] = ($smtp_config['ssl'] ? 'ssl:/'.'/' : '') . $smtp_config['host'];
	$mail['port'] = $smtp_config['port'];
	$mail['auth'] = (boolean) ($smtp_config['username'] && $smtp_config['password']);
	$mail['auth_username'] = $smtp_config['username'];
	$mail['auth_password'] = $smtp_config['password'];

	$errorlog = 'ajherrorlog';
	$charset = $sys_config['charset'];
	$bbname = $sys_config['site_name'];
	$adminemail = $sys_config['site_admin_email'];
	$maildelimiter = NEW_LINE;
	$mailusername = 1;

	$email_subject = '=?'.$charset.'?B?'.base64_encode(str_replace("\r", '', str_replace("\n", '', $email_subject))).'?=';
	$email_message = chunk_split(base64_encode(str_replace("\r\n.", " \r\n..", str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", str_replace("\n\r", "\r", $email_message)))))));

	$email_from = $smtp_config['email_from'] ? $smtp_config['email_from'] : $smtp_config['mail'];
	$email_from = ($email_from == '' ? '=?'.$charset.'?B?'.base64_encode($bbname)."?= <$adminemail>" : (preg_match('/^(.+?) \<(.+?)\>$/',$email_from, $from) ? '=?'.$charset.'?B?'.base64_encode($from[1])."?= <$from[2]>" : $email_from));

	foreach(explode(',', $email_to) as $touser) {
		$tousers[] = preg_match('/^(.+?) \<(.+?)\>$/',$touser, $to) ? ($mailusername ? '=?'.$charset.'?B?'.base64_encode($to[1])."?= <$to[2]>" : $to[2]) : $touser;
	}
	$email_to = implode(',', $tousers);

	$headers = "From: $email_from{$maildelimiter}X-Priority: 3{$maildelimiter}X-Mailer: TTTuangou ".SYS_VERSION."{$maildelimiter}MIME-Version: 1.0{$maildelimiter}Content-type: text/".($html ? 'html' : 'plain')."; charset=$charset{$maildelimiter}Content-Transfer-Encoding: base64{$maildelimiter}";
	$mail['port'] = $mail['port'] ? $mail['port'] : 25;
	if(!$fp = msockopen($mail['server'], $mail['port'], $errno, $errstr, 3)) {
		$errorlog('SMTP', "($mail[server]:$mail[port]) CONNECT - Unable to connect to the SMTP server", 0);
		return false;
	}
	stream_set_blocking($fp, true);

	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != '220') {
		$errorlog('SMTP', "$mail[server]:$mail[port] CONNECT - $lastmessage", 0);
		return false;
	}

	fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO')." TTTuangou\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
		$errorlog('SMTP', "($mail[server]:$mail[port]) HELO/EHLO - $lastmessage", 0);
		return false;
	}

	while(1) {
		if(substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
			break;
		}
		$lastmessage = fgets($fp, 512);
	}

	if($mail['auth']) {
		fputs($fp, "AUTH LOGIN\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 334) {
			$errorlog('SMTP', "($mail[server]:$mail[port]) AUTH LOGIN - $lastmessage", 0);
			return false;
		}

		fputs($fp, base64_encode($mail['auth_username'])."\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 334) {
			$errorlog('SMTP', "($mail[server]:$mail[port]) USERNAME - $lastmessage", 0);
			return false;
		}

		fputs($fp, base64_encode($mail['auth_password'])."\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 235) {
			$errorlog('SMTP', "($mail[server]:$mail[port]) PASSWORD - $lastmessage", 0);
			return false;
		}

		$email_from = $mail['from'];
	}

	fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		fputs($fp, "MAIL FROM: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from).">\r\n");
		$lastmessage = fgets($fp, 512);
		if(substr($lastmessage, 0, 3) != 250) {
			$errorlog('SMTP', "($mail[server]:$mail[port]) MAIL FROM - $lastmessage", 0);
			return false;
		}
	}

	$email_tos = array();
	foreach(explode(',', $email_to) as $touser) {
		$touser = trim($touser);
		if($touser) {
			fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser).">\r\n");
			$lastmessage = fgets($fp, 512);
			if(substr($lastmessage, 0, 3) != 250) {
				fputs($fp, "RCPT TO: <".preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser).">\r\n");
				$lastmessage = fgets($fp, 512);
				$errorlog('SMTP', "($mail[server]:$mail[port]) RCPT TO - $lastmessage", 0);
				return false;
			}
		}
	}

	fputs($fp, "DATA\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 354) {
		$errorlog('SMTP', "($mail[server]:$mail[port]) DATA - $lastmessage", 0);
		return false;
	}

	$headers .= 'Message-ID: <'.gmdate('YmdHs').'.'.substr(md5($email_message.microtime()), 0, 6).rand(100000, 999999).'@'.$_SERVER['HTTP_HOST'].">{$maildelimiter}";

	fputs($fp, "Date: ".gmdate('r')."\r\n");
	fputs($fp, "To: ".$email_to."\r\n");
	fputs($fp, "Subject: ".$email_subject."\r\n");
	fputs($fp, $headers."\r\n");
	fputs($fp, "\r\n\r\n");
	fputs($fp, "$email_message\r\n.\r\n");
	$lastmessage = fgets($fp, 512);
	if(substr($lastmessage, 0, 3) != 250) {
		$errorlog('SMTP', "($mail[server]:$mail[port]) END - $lastmessage", 0);
		return false;
	}

	fputs($fp, "QUIT\r\n");

	return true;
}

?>