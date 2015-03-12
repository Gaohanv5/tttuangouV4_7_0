<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name validate.han.php
 * @date 2014-09-01 17:24:22
 */
 



class ValidateHandler
{
	var $InvalidMessage;			var $InvalidCount;				var $Data;						var $PatternList=array();  	var $Debug=true;				var $DebugString='';		
	 
	function ValidateHandler()
	{
		$this->Invalid=array();
		$this->InvalidCount=0;
		$this->PatternList['email']="~^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+([a-z]{2,4})|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$~i";
		$this->PatternList['url']="/^(https?\:\/\/|www\.)([A-Za-z0-9_\-]+\.)+[A-Za-z]{2,4}(\/[\w\d\/=\?%\-\&_~`@\[\]\:\+\#]*([^<>\'\"\n])*)?$/";
		$this->PatternList['qq']="/^\d{5,9}$/";
		$this->PatternList['zip']="/^\d{6}$/";
		$this->PatternList['idcard']="/^\d{15}(\d{2}[A-Za-z0-9])?$/";
		$this->PatternList['chinese']="~^(?:[\x7f-\xff][\x7f-\xff])+$~";
		$this->PatternList['lettersonly']="/^[A-Za-z]+$/";
		$this->PatternList['alphanumeric']  = '/^[a-zA-Z0-9]+$/';
		$this->PatternList['numeric' ] = '/(^-?\d\d*\.\d*$)|(^-?\d\d*$)|(^-?\.\d\d*$)/';
		$this->PatternList['nopunctuation']= '/^[^().\/\*\^\?#!@$%+=,\"\'><~\[\]{}]+$/';
		$this->PatternList['mobile']="/^((\(\d{3}\))|(\d{3}\-))?13\d{9}$/";
		$this->PatternList['phone']="/^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}$/";
	}

	 
	function Validate($valid,$varname='')
	{
		if($varname!='')
		{
			if(is_array($varname))
			{
				$this->Data=$varname;
			}
			else
			{
				$this->VarName=$varname;
				$pos=strpos($varname,'[');
				if($pos===false)
				{
					eval("\$varvalue=\$GLOBALS[\"".ltrim($varname,'$')."\"];");
				}
				else
				{
					eval("\$varvalue=\$GLOBALS[\"".substr(substr_replace($varname,'"][',$pos,1),1).";");
				}
				$this->Data=$varvalue;
			}

		}
		else
		{

			$this->Data=$GLOBALS;

		}
		
		if(is_array($valid))
		{
			foreach($valid as $val)
			{
				list($name,$regular,$message)=explode('::',$val);
				$this->IsValid($this->Data[$name],$regular,$message,$name);
			}
		}
		else
		{
			list($name,$regular,$message)=explode('::',$valid);
			$this->IsValid($this->Data[$name],$regular,$message,$name);
		}
		Return ($this->InvalidCount>0)?false:true;

	}

	
	function RecordInvalid($name,$message)
	{
		if(!$name)
		{
			$this->Invalid[]=$message;
		}
		else
		{
			if(array_key_exists($name,$this->Invalid))
			{
				if($this->Invalid[$name]!=$message)
				{
					$this->Invalid[$name]=$this->Invalid[$name].",".$message;
				}
			}
			else
			{
				$this->Invalid[$name]=$message;
			}
		}

		$this->InvalidCount++;
	}

	
	function IsValid($data,$reqular,$message=null,$name=false,$reverse=false)
	{
		$regexp=$this->SetPattern($reqular);
		if(trim($regexp)!='')
		{
			$result=preg_match($regexp,$data);
			if($reverse==true)
			{
				$result=($result==true)?false:true;
			}

			if($result==false)
			{
				$this->RecordInvalid($name,$message);
			}

			if($this->Debug==true)
			{
				$this->Debug($name,$data,$regexp,$result,$message);
			}
		}

		Return $result;
	}


	
	function GetInvalid()
	{
		if($this->InvalidCount>0)
		{
			$i=0;
			$error_str="错误如下:<BR>\r\n";
			foreach($this->Invalid as $error)
			{
				$i++;
				$error_str.="\t{$i}.{$error}<BR>\r\n";
			}
			$this->Invalid=array();
			$this->InvalidCount=0;
			Return $error_str;
		}
		Return 0;

	}

	
	function GetInvalidCount()
	{
		Return $this->InvalidCount;
	}
	function GetCountInvalid()
	{
		Return $this->InvalidCount;
	}

	
	function SetPattern($name)
	{
		if(array_key_exists($name,$this->PatternList)) 		{
			Return $this->PatternList[$name];
		}
		else
		{
			if(preg_match("~^\{\d+(,)?(?(1)\d*?|0{0})\}$~",$name)) 			{
				Return "~^.".$name."$~s";
			}
			else
			{
				Return $name;
			}

		}

	}

	
	function AddPattern($name,$value)
	{
		$this->PatternList[$name]=$value;
	}

	
	function Debug($var,$name,$regexp,$result,$errormsg)
	{
		if(isset($this->VarName))
		{
			$var=$this->VarName.'["'.$var.'"]';
		}
		else
		{
			$var='$'.$var;
		}
		$red=$result?NULL:'COLOR="#FF0033"';
		$result=$result?__('验证成功'):__('验证失败');
		$this->DebugString.= '<FONT '.$red.'><B>变量名:'.$var.'('.$result.')</B></FONT><BR>';
		$this->DebugString.= '<B>字符串:</B>&nbsp;'.$name."<BR>";
		$this->DebugString.= '<B>表达式:</B>&nbsp;'.$regexp."<BR>";
		$this->DebugString.= '<B>错误提示:</B>&nbsp;'.$errormsg."<hr>";
	}
	function ShowDebug()
	{
		echo $this->DebugString;
	}
}

?>