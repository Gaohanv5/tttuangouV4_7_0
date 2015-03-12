<?php

/**
 * 模块：基于WEB的入侵检测系统
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name wips.mod.php
 * @version 1.0
 */

class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
		$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	function main()
	{
		$this->CheckAdminPrivs('wips');
		include handler('template')->file('@admin/wips_index');
	}
	function sql()
	{
		$this->CheckAdminPrivs('wips');
		include handler('template')->file('@admin/wips_sql_config');
	}
	function sql_save()
	{
		$this->CheckAdminPrivs('wips');
				ini('wips.sql.dfunction', post('dfunction'));
		ini('wips.sql.daction', post('daction'));
		ini('wips.sql.dnote', post('dnote'));
		ini('wips.sql.afullnote', post('afullnote'));
		ini('wips.sql.dlikehex', post('dlikehex'));
		ini('wips.sql.foradm', post('foradm'));
		ini('wips.sql.autoups', post('autoups'));
				$this->Messager('保存成功！', '?mod=wips');
	}
	public function status_ajax()
	{
		$this->CheckAdminPrivs('wips','ajax');
		if (ini('wips.sql.enabled'))
		{
			$string = 'WIPS已开启';
			if (ini('wips.sql.autoups') == 'true')
			{
				$lastc = fcache('wips.sql.rule.sync', 86400);
				if ($lastc)
				{

				}
				else
				{
															$server = base64_decode('aHR0cDovL3NxbC50dHR1YW5nb3UubmV0L3dpcHMvdXBkYXRlLnBocA==');
					$r = dfopen($server, 10485760, '', '', true, 10, 'CENWOR.TTTG.WIPS.SYNC.AGENT.'.SYS_VERSION.'.'.SYS_BUILD);
					if ($r)
					{
						$data = (array)json_decode($r, true);
						if (isset($data['hash']) && $data['hash'])
						{
							foreach ($data['rules'] as $rk => $rv)
							{
								if (substr($rk, 0, -4) == '.md5')
								{
									continue;
								}
								if (md5($rv) == $data['rules'][$rk.'.md5'])
								{
									if (ini('wips.sql.'.$rk) != $rv)
									{
																				ini('wips.sql.'.$rk, $rv);
										$updated = true;
									}
								}
							}
						}
					}
					fcache('wips.sql.rule.sync', 'lastCheck @ '.date('Y-m-d H:i:s', time()));
					if ($updated)
					{
						$string = 'WIPS自动升级完成';
					}
				}
			}
		}
		else
		{
			$string = 'WIPS未开启，有风险';
		}
		exit('<a href="admin.php?mod=wips">'.$string.'</a>');
	}
}

?>