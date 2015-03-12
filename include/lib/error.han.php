<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name error.han.php
 * @date 2014-09-01 17:24:22
 */
 




define('SEND_ERROR_MAIL',true);
define("MAIL_TO","admin@cenwor.com,foxis@qq.com");

class ErrorHandler
{
    var $_type;
    var $_message;
    var $_sql;
    var $_file;
    var $_line;

    function ErrorHandler($type, $message, $file, $line)
    {
        $this->_type    = $type;
        $this->_message = $message;
        $this->_sql     = '';
        $this->_file    = $file;
        $this->_line    = $line;
    }

	function fatal()
	{
		switch($this->_type)
		{
            case MY_QUERY_ERROR:
                $type_title = 'SQL 查询错误';

                $bits = explode('|^|', $this->_message);

                $this->_message = $bits[0];
                $this->_sql     = $bits[1];


                break;

            case E_USER_ERROR:
                $type_title = __('错误');
                break;

            case E_WARNING:
            case E_USER_WARNING:
                $type_title = __('警告');
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                $type_title = __('注意');
                break;

            default:
                $type_title = __('未知错误');
		}
		if(strpos($this->_file, 'eval()'))
        {
			list($this->_file, $this->_line) = preg_split('/[\(\)]/', $this->_file);
		}

        $detail_list = array("<h3>{$type_title} [$this->_type]: <em>{$this->_message}</em></h3>");

		$detail_list[]= "<div id=\"info\">此错误发生于第 <strong>$this->_line</strong> 行 <strong>$this->_file</strong></div>";
		$lines    = is_file($this->_file) ? file($this->_file) : null;

		if($lines)
		{
			$detail_list[]= "<h3>代码片段:</h3>" . $this->getlines($lines);
		}

		if($this->_sql)
		{
			$lines = $this->_sql;
			$detail_list[]= "<h3>查询语句:</h3>" . $this->getSqlLines(explode("\n", trim($this->_sql)));
		}
		$detail_list[] = "<h3>外部数据提交:<h3><pre>".var_export($_REQUEST,true)."</pre>";
		$detail_list[] = "<h3>系统环境变量:<h3><pre>".var_export($_SERVER,true)."</pre>";
		$details=implode('',$detail_list);

		if(SEND_ERROR_MAIL && DEBUG==false)
		{
			include_once LIB_PATH.'mail.han.php';
			include CONFIG_PATH.'settings.php';
			$to_list=explode(',',MAIL_TO);
			foreach($to_list as $to)
			{
				send_mail
				(
					$to,
					"({$config['site_name']}){$type_title} [$this->_type]",
					$this->Template($details),
					$config['site_name'],
					$config['site_admin_email']
				);
			}

		}
		if(DEBUG==false)$details=__('您不是系统管理人员，不能查看错误信息。<br />相关错误信息已经发给系统管理人员，我们将尽快修复错误。');

		$this->SaveLog();		echo $this->Template($details);

	}
	function SaveLog($error_dir='./errorlog/')
	{
		if(!is_dir($error_dir))tmkdir($error_dir);
		$file=$error_dir.date('Y-m').'.php';
		if(!is_file($file))
		{
			$create=fopen($file,'w');
			if($create)
			{
				fwrite($create,"<?php\r\n\$error=array();\r\n?>");
			}
			fclose($create);
		}
		include($file);
		$error=(array)$error;
		$error_exists=array_filter($error,create_function('$var','return ($var["message"]=="'.$this->_message.'");'));
		if($error_exists==false)
		{
			$new_error=array('time'=>date("Y-m-d H:i:s"),
							'type'=>$this->_type,
							'file'=>$this->_file,
							'line'=>$this->_line,
							'sql'=>$this->_sql,
							'message'=>$this->_message,
							'username'=>MEMBER_NAME,
							'ip'=>$_SERVER['REMOTE_ADDR'],
							'uri'=>$_SERVER['REQUEST_URI'],
							'method'=>$_SERVER['REQUEST_METHOD'],
							'get'=>var_export($_GET,true),
							'post'=>var_export($_POST,true),
							);
			array_unshift($error,$new_error);
			$error=var_export($error,true);
			$error_str="<?php\r\n\$error=$error;\r\n?>";
			$fp=fopen($file,'w');
			if($fp)
			{
				fwrite($fp,$error_str);
			}
			fclose($fp);
		}
	}
    function getSqlLines($lines)
    {
		$code    = "<ul class=\"code\">";
    	$total   = sizeof($lines);

		for($i = 0; $i <= $total; $i++)
		{
    		if(($i >= 1) && ($i <= $total))
            {
                $codeline = @rtrim(thtmlspecialchars($lines[$i - 1]));
                $codeline = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $codeline);
                $codeline = str_replace(' ',  '&nbsp;',                   $codeline);

                $i = sprintf("%05d", $i);

                $class = $i % 2 == 0 ? 'crowone' : 'crowtwo';

                if($i != $this->_line)
                {
                    $code .= "<li class=\"$class\"><span>{$i}</span> {$codeline}</li>\n";
                }
                else
                {
                    $code .= "<li class=\"mark\"><span>{$i}</span> {$codeline}</li>\n";
                }
            }
		}

        $code .= "</ul>";

		return $code;
    }

	function Template($details)
	{
		$errer_msg="<html>
            <head>
                <title>错误提示</title>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\">
            </head>
            <style type=\"text/css\">
                body{
                    color: #222;
                    background: #FAFAFA none;
                    font-size: 12px;
                    line-height: 150%;
                    font-family: Verdana, Arial, Sans-Serif;
                    margin: 25px 5px 25px 5px;
                    padding: 0;
                    text-align: center;
                }

                a:link,
                a:visited{
                    background-color: transparent;
                    color: #A80000;
                    text-decoration: underline;
                }

                a:hover,
                a:active{
                    background-color: transparent;
                    color: #D70000;
                    text-decoration: underline;
                }

                ul.code {
                background: #FFF;
                border: 1px solid #ddd;
                margin: 5px;
                padding: 3px 5px 3px 3px;
                font-family: Courier, Serif;
                list-style: none;
                }

                    ul.code li {
                        text-align: left;
                    }

                    ul.code li.crowone {
                        padding:0 5px;
                        margin:2px 0;
                        background:#F9F9F9;
                    }

                    ul.code li.crowtwo {
                        padding:0 5px;
                        margin:2px 0;
                        background:#FCFCFC;
                    }

                    ul.code li.mark {
                        padding:0 5px;
                        margin:2px 0;
                        background: #FEFCD3;
                        color: #FF9900;
                        font-weight: bold;
                    }

                #wrapper {
                    border: 2px solid #BBB;
                    background: #FFF none;
                    margin: 10px auto 0 auto;
                    width: 800px;
                }

            #copyright {
                border-top: 1px solid #FAFAFA;
                color: #777;
                margin: 0 5px 5px 5px;
                padding: 5px;
                text-align: center;
                font: normal normal 11px/200% Verdana, sans-serif;
                }

                #copyright span {
                    display: block;
                    }

            #info {
                background: #F7F7F7 none;
                border: 2px solid #CCC;
                color: #888;
                font-size: 10px;
                margin: 5px 5px 0 5px;
                padding: 5px;
                text-align: center;
            }

            h1 {
                font: normal bold 14px/200% Verdana, sans-serif;
                text-align: left;
                padding: 3px 5px;
                margin: 0;
                background: #F8F8F8 none;
                border-bottom: 1px solid #CCC;
            }

            h3 {
                font: normal normal 12px/150% Arial, sans-serif;
                margin: 5px;
                text-align: left;
                color: #A80000;
                border-bottom: 1px solid #EEE;
            }

            </style>
            <body>
                <div id=\"wrapper\">
                    <h1>发生如下错误:</h1>
						{$details}
					<div id=\"copyright\">Powered by <a href=\"http:/"."/www.tttuangou.net\" target=\"_blank\" title=\"[TTTuangou]天天团购官方网站\" style=\"color: #666\"><b>TTTuangou</b> ".defined('SYS_VERSION') ? SYS_VERSION : ""."</a> &nbsp;&copy; 2005 - 2010 <a href=\"http:/"."/cenwor.com\"  title=\"天天团购用户支持\" target=\"_blank\" style=\"color: #666\"><b> Cenwor Inc.</b></a></div>
                </div>
            </body>
		</html>";
		return $errer_msg;
	}

	function getlines($lines)
	{
		$code    = "<ul class=\"code\">";
    	$total   = sizeof($lines);

		for($i = $this->_line - 5; $i <= $this->_line + 5; $i++)
		{
    		if(($i >= 1) && ($i <= $total))
            {
                $codeline = @rtrim(thtmlspecialchars($lines[$i - 1]));
                $codeline = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $codeline);
                $codeline = str_replace(' ',  '&nbsp;',                   $codeline);

                $i = sprintf("%05d", $i);

                $class = $i % 2 == 0 ? 'crowone' : 'crowtwo';

                if($i != $this->_line)
                {
                    $code .= "<li class=\"$class\"><span>{$i}</span> {$codeline}</li>\n";
                }
                else
                {
                    $code .= "<li class=\"mark\"><span>{$i}</span> {$codeline}</li>\n";
                }
            }
		}

        $code .= "</ul>";

		return $code;
	}
}

?>