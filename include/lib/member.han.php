<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name member.han.php
 * @date 2014-12-11 14:44:49
 */
 


class MemberHandler
{
	var $ID;
	var $sid=0;

	var $session=array();

	var $SessionExists=false;

	var $MemberName;
	var $MemberPassword;
	var $MemberFields;
	var $Actions;
	var $CurrentAction;	var $_Error;
	var $_System;

	var $CookieHandler=null;
	var $DatabaseHandler=null;
	var $Config=array();

	function MemberHandler()
	{
		$this->DatabaseHandler	=dbc();
		$this->CookieHandler	=handler('cookie');
		$this->Config			=ini('settings');

		$this->ID				=0;
		$this->MemberName		='';
		$this->MemberPassword	='';
		$this->ActionList		='';
		$this->CurrentActions	='';

		$this->setSessionId();
	}
	function setSessionId($sid=null)
	{
		if($sid!==null)
		{
			$this->sid=$sid;
			$this->CookieHandler->SetVar('sid',$sid,86400*365);
		}
		else
		{
			$cookie_sid = $this->CookieHandler->GetVar('sid');
			if(empty($this->sid))
			{
				$this->sid = ($cookie_sid ? $cookie_sid : ($_GET['sid'] ? $_GET['sid'] : $_POST['sid']));
			}
		}
                if(!empty($this->sid)) {
            if(false == preg_match('~^[\w\d]{2,18}$~i', $this->sid)) {
								$this->setSessionId(random(6));
			}
        }
		return $this->sid;
	}
	
	function SetMember($user)
	{
		if(trim($user)!='')
		{
			$this->MemberName=$user;
		}
		else
		{
			Return false;
		}

	}
	
	function SetPassword($pass)
	{
		if(trim($pass)!='')
		{
			$this->MemberPassword=$pass;
		}
		else
		{
			Return false;
		}

	}
	function FetchMember($id, $pass,$secques='')
	{
        $this->ID   = (int) $id;
        $this->MemberPassword = trim($pass);
        $this->Secques=trim($secques);
		$this->MemberFields=$this->GetMember();
		if($this->MemberFields)
		{
			define("MEMBER_ID",(int) $this->MemberFields['uid']);
			define("MEMBER_TRUENAME",$this->MemberFields['truename']);
			define("MEMBER_NAME",$this->MemberFields['username']);
			define("MEMBER_ROLE_ID",(int) $this->MemberFields['role_id']);
			define("MEMBER_ROLE_NAME",$this->MemberFields['role_name']);
			define("MEMBER_ROLE_TYPE",$this->MemberFields['role_type']);
			define('AIJUHE_FOUNDER',(boolean) (MEMBER_ID > 0 && isset($this->Config['aijuhe_founder']) && false!==strpos(",{$this->Config['aijuhe_founder']},",",".MEMBER_ID.",")));
		}
        return true;
	}

	function UpdateSessions()
	{
		$onlinehold		=900;		$onlinespan		=$this->Config['onlinespan']=15;		$pvfrequence	=60;

		$session		=array();
		$session 		=$this->session;
		$timestamp		=time();
		extract($session);
		$uid			=(int)$this->MemberFields['uid'];
		$username		=$this->MemberFields['username'];
		$groupid		=(int)$session['groupid'];

				if ($uid && $onlinespan && ($timestamp-($session['lastolupdate']?$session['lastolupdate']:$session['lastactivity']))>$onlinespan*60)
		{
			$session['lastolupdate']=$timestamp;
			$sql="
			UPDATE ".TABLE_PREFIX.'system_onlinetime'."
			SET
				thismonth=thismonth+{$onlinespan},
				`total`=`total`+{$onlinespan},
				lastupdate={$timestamp}
			WHERE
				uid=".$uid."
				AND lastupdate<='".($timestamp-$onlinespan*60)."'";
			$this->DatabaseHandler->Query($sql,"UNBUFFERED");
			if (!$this->DatabaseHandler->AffectedRows())
			{
				$sql="
				REPLACE INTO ".TABLE_PREFIX.'system_onlinetime'."
					(thismonth,total,lastupdate,uid)
				values
					({$onlinespan},{$onlinespan},{$timestamp},".$uid.")";
				$this->DatabaseHandler->Query($sql,'SKIP_ERROR');
			}
		}

		$session['action']=$this->CurrentAction['id'];
		if ($this->CookieHandler->GetVar('sid')=='' || $this->sid!=$this->CookieHandler->GetVar('sid'))
		{
			$this->setSessionId($this->sid);
		}

				if($this->SessionExists==true)
		{
						if($pvfrequence && $uid)
			{
				if($session['spageviews']>=$pvfrequence)
				{
					$sql="
					UPDATE
						".TABLE_PREFIX.'system_members'."
					SET
						pageviews=pageviews+{$session['spageviews']}
					WHERE
						uid=".$uid;
					$this->DatabaseHandler->Query($sql);
					$pageviewsadd = ', pageviews=\'0\'';
				}
				else
				{
					$pageviewsadd = ', pageviews=pageviews+1';
				}
			}
			else
			{
				$pageviewsadd = '';
			}
			$sql="UPDATE ".TABLE_PREFIX.'system_sessions'." SET uid='$uid', username='$username', groupid='$groupid', styleid='{$session['styleid']}', invisible='{$session['invisible']}', action='{$session['action']}', lastactivity='$timestamp', lastolupdate='{$session['lastolupdate']}', seccode='{$session['seccode']}', fid='{$session['fid']}', tid='{$session['tid']}', bloguid='{$session['blogid']}' $pageviewsadd WHERE sid='{$this->sid}'";
			$this->DatabaseHandler->Query($sql);
		}
		else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
			$ips=explode('.',$ip);
			$sql="
			DELETE FROM ".TABLE_PREFIX.'system_sessions'."
			WHERE
				sid='{$this->sid}'
				OR lastactivity<($timestamp-$onlinehold)
				OR 	('".$uid."'<>'0' AND uid='".$uid."')
				OR 	(uid='0' AND ip1='$ips[0]' AND ip2='$ips[1]' AND ip3='$ips[2]' AND ip4='$ips[3]' AND lastactivity>$timestamp-60)";
			$this->DatabaseHandler->Query($sql);

			$sql="
			INSERT INTO ".TABLE_PREFIX.'system_sessions'." (sid, ip1, ip2, ip3, ip4, uid, username, groupid, styleid, invisible, action, lastactivity, lastolupdate, seccode, fid, tid, bloguid)
			VALUES ('{$this->sid}', '$ips[0]', '$ips[1]', '$ips[2]', '$ips[3]', '$uid', '$username', '$groupid', '{$session['styleid']}', '{$session['invisible']}', '{$session['action']}', '$timestamp', '{$session['lastolupdate']}', '{$session['seccode']}', '{$session['fid']}', '{$session['tid']}', '{$session['bloguid']}')";
			$this->DatabaseHandler->Query($sql,"SKIP_ERROR");
									if($uid && $timestamp - $session['lastactivity'] > 21600)
			{
				if($oltimespan && $timestamp - $session['lastactivity'] > 86400)
				{
					$sql="SELECT total FROM ".TABLE_PREFIX.'system_onlinetime'." WHERE uid='$uid'";
					$query = $this->DatabaseHandler->Query($sql);
					$oltime=$query->GetRow();
					$oltimeadd = ', oltime='.round(intval($oltime['total']) / 60);
				}
				else
				{
					$oltimeadd = '';
				}
				$sql="
				UPDATE
					".TABLE_PREFIX.'system_members'."
				SET
					lastip='$ip',
					lastvisit=lastactivity,
					lastactivity='$timestamp'
					$oltimeadd
				WHERE
					uid='".$uid."'";
				$this->DatabaseHandler->Query($sql,'mysql_unbuffered_query');
			}
		}
	}

		function HasPermission($mod,$act,$is_admin=0)
	{
		return admin_priv('index');
	}

	function SetLogItemId($id)
	{
		$this->Active['item_id']=(int)$id;
	}
	function SetLogItemTitle($title)
	{
		$this->Active['item_title']=$title;
	}
		function SetLogCredits($field,$credit=0)
	{
		$this->Active[$field]=$credit;
	}
	
	function SetLogURI($uri)
	{
		$this->Active['uri'] = $uri;
	}
	function SetLogUserId($user_id)
	{
		$this->Active['uid'] = $user_id;
	}
	function SetLogUsername($username)
	{
		$this->Active['username'] = $username;
	}
		function SaveActionToLog($title,$auto = true)
	{
		if($this->CurrentAction['log']==false and true===$auto)
		{
			Return ;
		}
		$this->SetLogItemTitle($title);
				if($this->Active['item_id']==0 and
			@substr_count($this->CurrentAction['action'],$this->Active['action'])>1)
		{
			Return '';
		}
		if($this->IsLogged!=true)
		{
			if($this->Active['item_title']==false)
			{
				$this->Active['item_title']=$this->Title;
			}
			$this->Active['action_id']=$this->CurrentAction['id'];
			unset($this->Active['id']);
			$this->DatabaseHandler->SetTable(TABLE_PREFIX. 'system_log');
			if($this->Active['item_id']!=0 and true===$auto)
			{
				$this->DatabaseHandler->Replace($this->Active);
			}
			else
			{
				$this->DatabaseHandler->Insert($this->Active);
			}
		}
		$this->IsLogged=true;
	}
	function _SetCurrentAction($action)
	{
		$this->CurrentAction=$action;
	}
	function GetCurrentAction()
	{
		Return $this->CurrentAction;
	}
	function GetMemberFields()
	{
		return $this->MemberFields;
	}
	
	function CheckMember($user,$password,$clsF=true)
	{
				$user = account()->username($user);
		$type = 'username';

		$this->SetMember($user);

		if(trim($user)!='')
		{
			$sql="
			Select
				*
			FROM
				".TABLE_PREFIX.'system_members'."
			WHERE
				`{$type}`='{$this->MemberName}'";
			$query = $this->DatabaseHandler->Query($sql);
			$this->MemberFields=$query->GetRow();

			if($this->MemberFields!=false)
			{
				$password_hash = account()->password($password, $this->MemberFields['email2']);
				if(in_array($this->MemberFields['password'], array($password, md5($password), $password_hash, md5($password_hash))))
				{
					Return 1;
				}
				else
				{
					$clsF && $this->MemberFields=array();
					Return -1;
				}
			}
			else
			{
				Return 0;
			}
		}
	}
    function GetMember()
    {
        $membertablefields = 'M.*';
        if($this->sid)
        {
        	if($this->ID)
        	{
				$sql="
		        SELECT
					$membertablefields,
					S.sid,
					S.styleid,
					S.lastactivity,
					S.lastolupdate,
					S.pageviews as spageviews,
					S.seccode
		        FROM
					".TABLE_PREFIX.'system_members'." `M`
					LEFT JOIN ".TABLE_PREFIX.'system_sessions'." S ON(M.uid=S.uid)
		        WHERE
					M.uid       = {$this->ID} AND
					M.password = '".$this->MemberPassword."' AND
					M.secques	='{$this->Secques}' AND
					S.sid='{$this->sid}' AND
					CONCAT_WS('.',S.ip1,S.ip2,S.ip3,S.ip4)='{$_SERVER['REMOTE_ADDR']}'
				";
        	}
        	else
        	{
				$sql="
				SELECT
					sid, groupid, pageviews as spageviews,uid AS sessionuid, lastolupdate,lastactivity,seccode
				FROM
					".TABLE_PREFIX.'system_sessions'."
				WHERE
					sid='{$this->sid}' AND CONCAT_WS('.',ip1,ip2,ip3,ip4)='{$_SERVER['REMOTE_ADDR']}'";
        	}

	        $query = $this->DatabaseHandler->query($sql);
	        	        if (($this->session=$query->GetRow())!=false)
	        {
		        $this->SessionExists=true;

	        }
	        else
	        {
				$sql="
				SELECT
					sid, groupid, pageviews, lastolupdate,lastactivity,seccode
				FROM
					".TABLE_PREFIX.'system_sessions'."
				WHERE
					sid='{$this->sid}' AND CONCAT_WS('.',ip1,ip2,ip3,ip4)='{$_SERVER['REMOTE_ADDR']}'";
				$query = $this->DatabaseHandler->Query($sql);
				$this->session=$query->getRow();
				if($this->session)
				{
					$this->SessionExists=true;
					$this->CookieHandler->DeleteVar('sid','auth');
				}
	        }
        }

		if($this->SessionExists==false)
		{
			if($this->ID)
			{
				$sql="
		        SELECT
					$membertablefields
		        FROM
					".TABLE_PREFIX.'system_members'." `M`
		        WHERE
					M.uid       = {$this->ID} AND
					M.password = '".$this->MemberPassword."' AND
					M.secques	='{$this->Secques}'";
		        $query = $this->DatabaseHandler->query($sql);
		        if (($this->session=$query->getRow())==false)
		        {
		        	$this->CookieHandler->DeleteVar('sid','auth');
		        }
			}
	        $this->sid=$this->session['sid']=random(6);
	        $this->session['seccode']=random(6,1);
		}
        return ($this->Sessions = $this->session);
    }
	function _SetError($error)
	{
		$this->_Error[]=$error;
	}
	function GetError()
	{
		Return $this->_Error;
	}
}
?>