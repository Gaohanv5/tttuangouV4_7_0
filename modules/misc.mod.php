<?php

/**
 * 模块：综合
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package module
 * @name misc.mod.php
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

    
    function Main()
    {
        header('Location: .');
    }

    
    function Region()
    {
        $parent = $this->Get['parent'];
        $parent = (is_numeric($parent)) ? ( int )$parent : 0;
        $list = logic('misc')->RegionList($parent);
        $ops = '';
        foreach ( $list as $i => $one )
        {
            $ops .= '<option value="' . $one['id'] . '">' . $one['name'] . '</option>';
        }
        echo $ops;
    }

    
    function Address()
    {
        $ops = array(
            'status' => 'failed', 'msg' => __('请求无效！')
        );
        echo jsonEncode($ops);
    }

    
    function Address_get()
    {
        $uid = $this->address_ops_admin() ? -1 : user()->get('id');
    	$id = post('id', 'int');
    	$address = logic('address')->GetOne($id, $uid);
    	if (!$address)
    	{
    		$ops = array(
            	'status' => 'failed', 'msg' => __('找不到相关地址信息！')
        	);
    	}
    	else
    	{
    		$loc = $address['region_loc'];
    		list(, $province, $city, $country, ,) = explode(',', $loc);
    		$array = array(
    			'id' => $address['id'],
    			'province' => $province,
    			'city' => $city,
    			'country' => $country,
    			'address' => $address['address'],
    			'zip' => $address['zip'],
    			'name' => $address['name'],
    			'phone' => $address['phone']
    		);
    		$ops = array(
    			'status' => 'ok',
    			'addr' => $array
    		);
    	}
    	echo jsonEncode($ops);
    }

    
    function Address_save()
    {
        $uid = $this->address_ops_admin() ? -1 : user()->get('id');
        $province = post('province', 'int');
        $city = post('city', 'int');
        $country = post('country', 'int');
        $post['region'] = ',' . $province . ',' . $city . ',' . $country . ',';
        $post['address'] = post('address', 'txt');
        $post['zip'] = post('zip', 'number');
        $post['name'] = post('name', 'txt');
        $post['phone'] = post('phone', 'txt');
        $id = post('id', 'int');
        if (!$id)
        {
        	$new_id = logic('address')->Add($uid, $post);
        }
        else
        {
        	$new_id = logic('address')->Update($id, $post, $uid);
        }
        if ( $new_id > 0 )
        {
            $ops = array(
                'status' => 'ok', 'id' => $new_id
            );
        }
        else
        {
            $ops = array(
                'status' => 'failed', 'msg' => __('保存失败！')
            );
        }
        echo jsonEncode($ops);
    }

    
	function Address_del()
	{
        $uid = $this->address_ops_admin() ? -1 : user()->get('id');
		$id = post('id', 'int');
		$result = logic('address')->Remove($id, $uid);
		if ($result > 0)
		{
			$ops = array(
				'status' => 'ok'
			);
		}
		else
		{
			$ops = array(
				'status' => 'failed',
				'msg' => __('删除失败！')
			);
		}
		echo jsonEncode($ops);
	}
    
    private function address_ops_admin()
    {
        if (get('from') != 'admin') return false;
        return user()->isAdminLogin();
    }

    
    function Express()
    {
        $ops = array(
            'status' => 'failed', 'msg' => __('请求无效！')
        );
        echo jsonEncode($ops);
    }

    
    function Express_list()
    {
        $aid = get('aid', 'int');
        $pid = get('pid', 'int');
        $list = logic('express')->GetList($aid, $pid);
        $ops = array(
            'status' => 'ok', 'html' => $list
        );
        echo jsonEncode($ops);
    }
    public function Recharge_cardifo()
    {
        $no = get('no', 'number');
        strlen($no) == 12 || exit('充值卡号码不正确！');
        $card = logic('recharge')->card()->ifo($no);
        $card || exit('充值卡号错误！');
        $card['status'] == RECHARGE_CARD_STA_Normal || exit('此充值卡已经不可用！');
        $card['usetime'] > 0 && exit('此充值卡已经被使用！');
        exit('面值：'.$card['price'].'元');
    }
}
?>