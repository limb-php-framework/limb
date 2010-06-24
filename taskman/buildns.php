<?php

include(dirname(__FILE__) . '/taskman.inc.php');

$funcs = get_defined_functions();

$out = <<<EOD
<?php

namespace taskman;

require_once(dirname(__FILE__) . '/taskman.inc.php'); 

function __(\$str)
{
  return \\taskman_str(\$str);
}


EOD;

foreach($funcs['user'] as $func)
{
  if(strpos($func, "taskman_") !== 0)
    continue;

  $ref = new ReflectionFunction($func);
  $args_decl = "";
  $args_pass = "";
  foreach($ref->getParameters() as $p)
  {
    if($p->isPassedByReference())
      $args_decl .= '&';

    $args_decl .= '$' . $p->getName();
    if($p->isDefaultValueAvailable())
    {
      $def_value = $p->getDefaultValue();
      switch(gettype($def_value))
      {
        case "boolean":
          $args_decl .= "=" . ($def_value ? 'true' : 'false');
          break;
        case "NULL":
          $args_decl .= "=null";
          break;
        case "string":
          $args_decl .= "='$def_value'";
          break;
        case "array":
          if($def_value !== array())
            throw new Exception("Default value '$def_value' is too complex for arg '" . $p->getName() . "' in func '$func'");
          $args_decl .= "=array()";
          break;
        default:
          throw new Exception("Type '" . gettype($def_value) . "' is not supported for default arg '" . $p->getName() . "' in func '$func'");
      }
    }
    $args_decl .= ", ";

    $args_pass .= '$' . $p->getName() . ",";
  }
  $args_decl = rtrim($args_decl, " ,");
  $args_pass = rtrim($args_pass, ",");

  $newfunc = str_replace("taskman_", "", $func);

  $out .= "function $newfunc($args_decl)\n";
  $out .= "{\n";
  $out .= "  return \\$func($args_pass);\n";
  $out .= "}\n\n";

}

echo $out;
