<?php

$config["wips"] =  array (
  'sql' => 
  array (
    'enabled' => true,
    'dfunction' => 'load_file,hex,substring,if,ord,char,ascii,mid,sleep',
    'daction' => 'intooutfile,intodumpfile,unionselect,unionall,uniondistinct,(select',
    'dnote' => '--',
    'afullnote' => 'true',
    'dlikehex' => 'true',
    'foradm' => 'false',
    'autoups' => 'true',
  ),
);
?>