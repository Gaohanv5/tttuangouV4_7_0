<?php

/**
 * 界面支持：即时搜索
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package UserInterface
 * @name isearcher.ui.php
 * @version 1.0
 */

class iSearcherUI
{
    
    public function load($idx)
    {
		        $map = ini('isearcher.map');
        $fidString = ini('isearcher.idx.'.$idx);
        $fids = explode(',', $fidString);
		        $filter = ini('isearcher.filter');
        $ffsString = ini('isearcher.frc.'.$idx);
        $frcs = explode(',', $ffsString);
				$timev = ini('isearcher.timev');
		$tvString = ini('isearcher.tvs.'.$idx);
		$tvss = explode(',', $tvString);
				$tvinputs = array();
		foreach ($tvss as $tvsk)
		{
			if (isset($_GET['iscp_tvbegin_'.$tvsk]))
			{
				$tvinputs[$tvsk]['begin'] = get('iscp_tvbegin_'.$tvsk, 'txt');
			}
			if (isset($_GET['iscp_tvfinish_'.$tvsk]))
			{
				$tvinputs[$tvsk]['finish'] = get('iscp_tvfinish_'.$tvsk, 'txt');
			}
		}
		$iscp_input_value = ($_GET['iscp_input_value'] ? $_GET['iscp_input_value'] : $_POST['iscp_input_value']);
		        include handler('template')->file('@html/isearcher/index');
    }
}

?>