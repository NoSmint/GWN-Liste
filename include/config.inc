<?php

function my_array_map($array) {
  $result = array();
  foreach ($array as $key => $val)
  {
      $result[$key] = (is_array($val) ? my_array_map($val) : htmlspecialchars($val, ENT_QUOTES | ENT_HTML5 | ENT_DISALLOWED | ENT_SUBSTITUTE, 'UTF-8'));
  }
  return $result;
}

$config = my_array_map(parse_ini_file(_ROOT_.'/config.ini', TRUE, INI_SCANNER_TYPED));

?>