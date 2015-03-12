<?php

/**
 * 逻辑区：订阅管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name subscribe.logic.php
 * @version 1.0
 */

class SubscribeLogic
{
    
    public function TypeList()
    {
        return array(
        	'mail' => '邮件',
        	'sms' => '短信'
        );
    }
    
    public function GetOne($id)
    {
        return dbc(DBCMax)->select('subscribe')->where(array('id'=>$id))->limit(1)->done();
    }
    
    function GetList($type = null, $city = null)
    {
        $sql_limit_type = '1';
        if (!is_null($type))
        {
            $sql_limit_type = 'type="'.$type.'"';
        }
        $sql_limit_city = '1';
        if (!is_null($city))
        {
            $sql_limit_city = 'city='.$city;
        }
        $sql = 'SELECT * FROM '.table('subscribe').' WHERE '.$sql_limit_type.' AND '.$sql_limit_city.' AND validated="true"';
        $sql = page_moyo($sql);
        return ($query = dbc()->Query($sql)) ? $query->GetAll() : array();
    }
    
    public function Add($city, $type, $target, $validated = 'false')
    {
        $one = dbc(DBCMax)->select('subscribe')->where(array('target'=>$target))->limit(1)->done();
        if(false == $one) {        
            $data = array(
                'type' => $type,
                'target' => $target,
                'city' => $city,
                'time' => time(),
                'validated' => $validated
            );
            dbc()->SetTable(table('subscribe'));
            $iid = dbc()->Insert($data);
        } else {
            $iid = $one['id'];
        }
        if ($validated == 'true')
        {
            $this->Validate($iid);
        }
        return $iid;
    }
    
    function Del($id)
    {
        dbc()->SetTable(table('subscribe'));
        dbc()->Delete('', 'id='.$id);
    }
    
    public function Validate($id, $validated = 'true')
    {
        if (!$id || !is_numeric($id)) return;
        return dbc(DBCMax)->update('subscribe')->data(array('validated'=>$validated))->where(array('id'=>$id))->done();
    }
    
    public function Subsd($target)
    {
        $result = $this->Search('target', $target, true);
        if ($result['validated'] == 'false') return false;
        return $result ? $result['id'] : false;
    }
    
    public function Search($field, $value, $getOne = true)
    {
        $dbc = dbc(DBCMax)->select('subscribe')->where(array($field=>$value));
        if ($getOne)
        {
            $dbc->limit(1);
        }
        return $dbc->done();
    }
    
    function Push($class, $city, $data)
    {
        $runCode = 'Push_'.$class;
        if ($city == -1)
        {
            $city = null;
        }
        $_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
        $list = $this->GetList($class, $city);
        if (count($list) == 0)
        {
            return;
        }
        $this->$runCode($list, $data);
    }
	
	public function Push_direct($class, $targets, $data)
	{
		$runCode = 'Push_'.$class;
        $targets = str_replace("\r", "", $targets);
        $list = explode("\n", $targets);
        if (count($list) == 0)
        {
            return;
        }
		foreach ($list as $i => $one)
		{
			$list[$i] = array(
				'type' => $class,
				'target' => $one
			);
		}
        $this->$runCode($list, $data);
	}
    private function Push_mail($list, $data)
    {
        foreach ($list as $i => $one)
        {
            logic('push')->add($one['type'], $one['target'], array('subject'=>$data['title'],'content'=>$data['content']));
        }
    }
    private function Push_sms($list, $data)
    {
        $phone = '';
        foreach ($list as $i => $one)
        {
            $phone .= $one['target'].';';
        }
        $phone = substr($phone, 0, -1);
        logic('push')->add('sms', $phone, $data);
    }
}

?>