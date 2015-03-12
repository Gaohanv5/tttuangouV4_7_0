<?php
/**
 * @copyright (C)2014 Cenwor Inc.
 * @author Cenwor <www.cenwor.com>
 * @package php
 * @name countdown.mod.php
 * @date 2014-12-11 14:44:49
 */
 




class ModuleObject extends MasterObject
{
	function ModuleObject( $config )
	{
		$this->MasterObject($config);
				$runCode = Load::moduleCode($this);
		$this->$runCode();
	}
	public function main(){
		logic('order')->FreeCountDownOrder();		$mutiView = true;
		if (ini('ui.igos.pager'))
		{

		}
		else
		{
			$_GET[EXPORT_GENEALL_FLAG] = EXPORT_GENEALL_VALUE;
		}
		$product = logic('product')->GetList(logic('misc')->City('id'), NULL, '`is_countdown`=1 AND p.overtime > '.time());
				$usePager = get('page', 'int');
		if (ini('ui.igos.dsper') && $mutiView && count($product) > 1)
		{
			logic('product')->reSort($product);
		}
		if($product){
			foreach($product as &$v){
				if( $v['begintime'] > time() ){
					$lasttime = $v['begintime'] - time();
					if( $lasttime > 2 * 60 *60 ){
						$v['begin_date'] = date('Y-m-d H:i:s',$v['begintime']);
					}else{
						$v['limit_time'] = $lasttime;
					}
				}
				if( $v['maxnum']==0 ){
					$v['num']=999;
				}else{
										$v['num'] = logic('product')->Surplus($v['maxnum'], logic('product')->SellsCount($v['id']));
				}
				if( $v['num']<0 ){
					$v['num']=0;
				}
				$v['pic'] = imager($v['imgs'][0],IMG_Original);
				$v['overtime'] = date('Y-m-d H:i:s', $v['overtime']);
			}
		}
		$this->Title = "限时抢购";
		include handler('template')->file('buy_countdown');
	}
}

?>