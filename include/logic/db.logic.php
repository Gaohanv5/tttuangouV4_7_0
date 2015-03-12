<?php

/**
 * 逻辑区：数据库管理
 * @copyright (C)2011 Cenwor Inc.
 * @author Moyo <dev@uuland.org>
 * @package logic
 * @name db.logic.php
 * @version 2.0
 * @changelog +
 * + 2012/01/16 = 1.0 > 1.2
 * 增加对字段索引的修复（初步）（限制：1、1个索引只能包含1个字段；2、索引名和字段名必须相同）
 * + 2013/11/15 = 1.2 > 2.0
 * 增加对联合索引字段的修复，完善1.2版本中未实现的功能
 * structAnalyze增加自动缓存管理
 * 增加structRepair结构修复接口（支持新建UNIQUE索引前自动填充随机内容）
 */

class DBMgrLogic
{
    
    public function structAnalyze($cacheMgr = 'auto')
    {
                        $tables = dbc(DBCMax)->query('SHOW TABLES')->done();
                $fileStruct = file_get_contents(DATA_PATH.'install/struct.sql');
                if ($cacheMgr == 'auto')
        {
            $ckgstring = serialize($tables).'$%^_^%$'.$fileStruct;
            $cksign = md5($ckgstring);
            $fc_k_ckg = 'logic.db.struct.analyze.ckg';
            $fc_k_data = 'logic.db.struct.analyze.data';
                        $ckginfo = fcache($fc_k_ckg, 86400);
                        fcache($fc_k_ckg, array('hash' => $cksign, 'access' => time()));
            if ($ckginfo)
            {
                if ($ckginfo['hash'] == $cksign)
                {
                    if (abs(time() - $ckginfo['access']) > 1)
                    {
                        $sdata = fcache($fc_k_data, 86400 * 7);
                        if ($sdata)
                        {
                            return $sdata;
                        }
                    }
                    else
                    {
                                            }
                }
            }
        }
                        $tableArrayKey = 'Tables_in_'.ini('settings.db_name');
        $tablePrefix = ini('settings.db_table_prefix');
        $tablePrefixLen = strlen($tablePrefix);
                $cmpResult = array();
        $dbTables = array();
        $relTBStructs = array();
        $relTBIndexs = array();
        foreach ($tables as $i => $tableArray)
        {
            $tableName = $tableArray[$tableArrayKey];
            if (substr($tableName, 0, $tablePrefixLen) != $tablePrefix)
            {
                continue;
            }
            else
            {
                $tableName = substr($tableName, $tablePrefixLen);
            }
            $dbTables[$tableName] = '_Moyo_';
                        $sqlSearch = 'CREATE TABLE `{prefix}'.$tableName.'`';
            if (!stristr($fileStruct, $sqlSearch))
            {
                continue;
            }
            preg_match_all('/CREATE TABLE `\{prefix\}'.$tableName.'`\s\((.*?)\)\sENGINE=MyISAM/is', $fileStruct, $st);
            if (!$st[0])
            {
                continue;
            }
            $relSTString = $st[1][0];
                        $relSTTabs = explode("\n", $relSTString);
            $relSTIndexs = array();
            $tabRight = array();
            foreach ($relSTTabs as $i => $_rTab)
            {
                                if (trim($_rTab) == '')
                {
                    continue;
                }
                $_rTab = str_replace(array("\n", "\r"), '', $_rTab);
                $_rTab = preg_replace('~\scomment\s+\'[^\']*?\'~i', '', $_rTab);
                if (substr($_rTab, -1) == ',')
                {
                    $_rTab = substr($_rTab, 0, -1);
                }
                                preg_match('/^\s+`([a-z0-9_]+)`/i', $_rTab, $_rTabMch);
                if ($_rTabMch[0])
                {
                    $_rTab_Field = $_rTabMch[1];
                }
                else
                {
                                        $_rTab_Field = false;
                                        preg_match_all('/(.*?[key|index]+)\s+(`([a-z0-9_]+)`\s+)?\(([a-z0-9_,`\s]+)+\)/i', $_rTab, $_rTabMch);
                    if ($_rTabMch[0])
                    {
                        foreach ($_rTabMch[0] as $_rmi => $_rmstring)
                        {
                            $type = trim($_rTabMch[1][$_rmi]);
                            $key = trim($_rTabMch[3][$_rmi]);
                            $fields = explode(',', str_replace(array('`', ' '), '', $_rTabMch[4][$_rmi]));
                            if ($key)
                            {
                                if (stristr($type, 'unique'))
                                {
                                    $type = 'UNIQUE';
                                }
                                else
                                {
                                    $type = 'INDEX';
                                }
                            }
                            else
                            {
                                if (strtoupper(substr($type, 0, 7)) == 'PRIMARY')
                                {
                                    $type = 'PRIMARY';
                                    $key = 'PRIMARY';
                                }
                            }
                            if (count($fields) < 2)
                            {
                                $relSTIndexs[$tableName][$key][] = array(
                                    'field' => end($fields),
                                    'type' => $type
                                );
                            }
                            else
                            {
                                foreach ($fields as $field)
                                {
                                    $relSTIndexs[$tableName][$key][] = array(
                                        'field' => $field,
                                        'type' => $type
                                    );
                                }
                            }
                        }
                    }
                }
                $_rTab = str_replace($_rTabMch[0], '', $_rTab);
                                preg_match('/default ([^ ,]*?)$/i', $_rTab, $_rTabMchDf);
                if ($_rTabMchDf)
                {
                    if (substr($_rTabMchDf[1], 0, 1) == "'")
                    {
                        $_rTab_Default = str_replace('\'', '', $_rTabMchDf[1]);
                    }
                    else
                    {
                        $_Val = $_rTabMchDf[1];
                        if (strtolower($_Val) == 'null')
                        {
                            $_rTab_Default = NULL;
                        }
                    }
                    $_rTab = str_replace($_rTabMchDf[0], '', $_rTab);
                }
                else
                {
                    $_rTab_Default = false;
                }
                $_rTab = str_replace($_rTabMch[0], '', $_rTab);
                                if (stristr($_rTab, 'not null'))
                {
                    $_rTab_Null = 'NO';
                }
                else
                {
                    $_rTab_Null = 'YES';
                }
                $_rTab = str_ireplace(array('not null', 'null'), '', $_rTab);
                                if (stristr($_rTab, 'auto_increment'))
                {
                    $_rTab_Extra = 'AUTO_INCREMENT';
                }
                else
                {
                    $_rTab_Extra = '';
                }
                $_rTab = str_ireplace('auto_increment', '', $_rTab);
                                $_rTab_Type = trim($_rTab);
                if ($_rTab_Field)
                {
                    $tabRight[$_rTab_Field] = array(
                        'Field' => $_rTab_Field,
                        'Type' => $_rTab_Type,
                        'Null' => $_rTab_Null,
                        'Default' => $_rTab_Default,
                        'Extra' => $_rTab_Extra
                    );
                }
            }
                        $curSTTabs = dbc(DBCMax)->query('DESCRIBE '.$tablePrefix.$tableName)->done();
                        $curSTIndexsSRC = dbc(DBCMax)->query('SHOW INDEX FROM '.$tablePrefix.$tableName)->done();
            $curSTIndexsSRC || $curSTIndexsSRC = array();
            $curSTIndexs = array();
            foreach ($curSTIndexsSRC as $i => $curSTIndex)
            {
                $__curTabIdx = &$curSTIndexs[$tableName];
                $__key = $curSTIndex['Key_name'];
                $__curTabIdx[$__key][] = array(
                    'field' => $curSTIndex['Column_name'],
                    'type' => $curSTIndex['Non_unique'] ? ($curSTIndex['Index_type'] == 'BTREE' ? 'INDEX' : $curSTIndex['Index_type']) : ($curSTIndex['Key_name'] == 'PRIMARY' ? 'PRIMARY' : 'UNIQUE')
                );
            }
            $tabCurrent = array();
            foreach ($curSTTabs as $i => $curSTTab)
            {
                $tabCurrent[$curSTTab['Field']] = $curSTTab;
            }
                        $tabIdxCMD = array();
            if (isset($relSTIndexs[$tableName]))
            {
                $rIdx = $relSTIndexs[$tableName];
                $rIdx || $rIdx = array();
                $cIdx = $curSTIndexs[$tableName];
                $cIdx || $cIdx = array();
                if ($rIdx)
                {
                    foreach ($rIdx as $idxName => $ridxDatas)
                    {
                        $cidxDatas = isset($cIdx[$idxName]) ? $cIdx[$idxName] : array();
                        $add = false;
                        if ($cidxDatas)
                        {
                            $drop = false;
                            if (count($cidxDatas) != count($ridxDatas))
                            {
                                $drop = true;
                                $add = true;
                            }
                            else
                            {
                                $ridxDM = array();
                                foreach ($ridxDatas as $ridxi => $ridxData)
                                {
                                    $ridxDM[$ridxData['field']] = $ridxData['type'];
                                }
                                $cidxDM = array();
                                foreach ($cidxDatas as $cidxi => $cidxData)
                                {
                                    $cidxDM[$cidxData['field']] = $cidxData['type'];
                                }
                                ksort($ridxDM);
                                ksort($cidxDM);
                                if ($ridxDM != $cidxDM)
                                {
                                    $drop = true;
                                    $add = true;
                                }
                            }
                            if ($drop)
                            {
                                $tabIdxCMD[$idxName]['DEL'][] = 'DROP INDEX `'.$idxName.'`';
                            }
                        }
                        else
                        {
                            $add = true;
                        }
                        if ($add)
                        {
                            $idxType = $ridxDatas[0]['type'];
                            $fields = array();
                            if (count($ridxDatas) > 1)
                            {
                                foreach ($ridxDatas as $ridxi => $ridxData)
                                {
                                    $fields[] = $ridxData['field'];
                                }
                            }
                            else
                            {
                                $fields[] = $ridxDatas[0]['field'];
                            }
                            $tabIdxCMD[$idxName]['ADD'][] = ' ADD '.$idxType.' `'.$idxName.'` (`'.implode('`,`', $fields).'`)';
                        }
                    }
                }
            }
                        $lastField = '';
            foreach ($tabRight as $field => $struct)
            {
                $cmd = false;
                if (!isset($tabCurrent[$field]))
                {
                    $cmd = 'ADD';
                }
                else
                {
                    $tabCurrent[$field]['Type'] = preg_replace('~\scomment\s+\'[^\']*?\'~i', '', $tabCurrent[$field]['Type']);
                    if (
                        strtolower($struct['Type']) != strtolower($tabCurrent[$field]['Type'])
                        ||
                        $struct['Null'] != $tabCurrent[$field]['Null']
                        ||
                        strtolower($struct['Default']) != strtolower($tabCurrent[$field]['Default'])
                        ||
                        strtolower($struct['Extra']) != strtolower($tabCurrent[$field]['Extra'])
                    )
                    {
                        $cmd = 'CHANGE';
                    }
                }
                if ($cmd)
                {
                    $curDATA = array(
                        'class' => 'field',
                        'table' => $tableName,
                        'field' => $field,
                        'cmd' => $cmd
                    );
                    $curDATA['sql'] =
                        'ALTER TABLE `'.($tablePrefix.$tableName).'` '
                        .$cmd.
                        ($cmd=='ADD'?'':(' `'.$field.'`'))
                        .' `'.$field.'` '
                        .$struct['Type']
                        .($struct['Null']=='NO'?' NOT NULL':'')
                        .($struct['Default']===false?'':(' DEFAULT \''.$struct['Default'].'\''))
                        .($struct['Extra']?(' '.$struct['Extra']):'')
                        .( ($struct['Extra'] && strtolower($struct['Extra']) == 'auto_increment') ? ' PRIMARY KEY' : '')
                        .( ($cmd=='ADD') ? ($lastField ? (' AFTER `'.$lastField.'`') : ' FIRST' ) : '');
                    $cmpResult[] = $curDATA;
                }
                $lastField = $field;
            }
                        foreach ($tabIdxCMD as $idxName => $cmds)
            {
                foreach ($cmds as $cmd => $sqls)
                {
                    $curDATA = array(
                        'class' => 'index',
                        'table' => $tableName,
                        'field' => $idxName,
                        'cmd' => $cmd,
                        'sql' => 'ALTER TABLE `'.($tablePrefix.$tableName).'`'.implode(',', $sqls)
                    );
                    $cmpResult[] = $curDATA;
                }
            }
                        $relTBStructs[$tableName] = $tabRight;
            $relTBIndexs = array_merge($relTBIndexs, $relSTIndexs);
        }
                        preg_match_all('/DROP TABLE IF EXISTS `\{prefix\}([a-z0-9_]+)`;/i', $fileStruct, $_rTabMch);
        $localTables = $_rTabMch[1];
        foreach ($localTables as $i => $tableName)
        {
            if (!isset($dbTables[$tableName]))
            {
                $curDATA = array(
                    'class' => 'table',
                    'table' => $tableName,
                    'field' => '*',
                    'cmd' => 'ADD'
                );
                preg_match_all('/CREATE TABLE `\{prefix\}'.$tableName.'`\s\(.*?\).*?CHARSET=utf8;/is', $fileStruct, $sqlMch);
                $sql = $sqlMch[0][0];
                $sql = str_ireplace('`{prefix}', '`'.$tablePrefix, $sql);
                $charset = ini('settings.charset');
                if ($charset == 'gbk')
                {
                    $sql = str_ireplace('utf8;', 'gbk;', $sql);
                }
                $curDATA['sql'] = $sql;
                $cmpResult[] = $curDATA;
            }
        }
                if ($cacheMgr == 'auto')
        {
            fcache($fc_k_data, array($cmpResult, $relTBStructs, $relTBIndexs));
        }
                return array($cmpResult, $relTBStructs, $relTBIndexs);
    }
    
    public function structRepair()
    {
        list($cmpResult, $tables, $indexs) = $this->structAnalyze();
        if ($cmpResult)
        {
            $results = array();
            foreach ($cmpResult as $cmpi => $cmpData)
            {
                if ($cmpData['class'] == 'index')
                {
                    if (stristr($cmpData['sql'], 'unique'))
                    {
                        if ($this->uniqueFilling($tables[$cmpData['table']], $indexs[$cmpData['table']], $cmpData['sql'], $cmpData['table']))
                        {
                            $results[] = $this->sqlQuery($cmpData['sql']);
                        }
                    }
                    else
                    {
                        $results[] = $this->sqlQuery($cmpData['sql']);
                    }
                }
                else
                {
                    $results[] = $this->sqlQuery($cmpData['sql']);
                }
            }
                        fcache('logic.db.struct.analyze.data', 0);
                        return $results;
        }
        else
        {
            return false;
        }
    }
    
    private function sqlQuery($sql)
    {
        $result = dbc(DBCMax)->query($sql)->done();
        if (is_numeric($result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    private function uniqueFilling($table, $index, $sql, $tableName)
    {
        $fillings = false;
        foreach ($index as $idxName => $idxFields)
        {
            if (stristr($sql, $idxName))
            {
                foreach ($idxFields as $idxi => $idxField)
                {
                    $filling = false;
                    $fieldStruct = $table[$idxField['field']];
                    if (preg_match('/^(var)?char\((\d+)\)$/i', $fieldStruct['Type'], $match))
                    {
                        $csize = $match[2];
                        if ($csize >= 32)
                        {
                            $filling = true;
                        }
                    }
                    elseif (preg_match('/text$/i', $fieldStruct['Type'], $match))
                    {
                        $filling = true;
                    }
                    if ($filling)
                    {
                                                $exists = dbc(DBCMax)->query('SELECT * FROM `'.ini('settings.db_table_prefix').$tableName.'` WHERE `'.$fieldStruct['Field'].'` != ""')->limit(1)->done();
                        if ($exists)
                        {
                                                        $fillings = true;
                        }
                        else
                        {
                            if ($this->sqlQuery('UPDATE `'.ini('settings.db_table_prefix').$tableName.'` SET `'.$fieldStruct['Field'].'` = UUID()'))
                            {
                                $fillings = true;
                            }
                        }
                    }
                }
            }
        }
        return $fillings;
    }
}

?>