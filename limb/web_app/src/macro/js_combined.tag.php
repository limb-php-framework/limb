<?php

/**
 * @tag js:combined
 * @aliases js_combined
 * @req_attributes dir
 * @restrict_self_nesting
 */  

class lmbJsCombinedMacroTag extends lmbMacroTag
{  
  protected function _generateContent($code)
  {
    if(!$root_dir = lmb_env_get('LIMB_DOCUMENT_ROOT', false))
      throw new lmbMacroException('Not set require env LIMB_DOCUMENT_ROOT!');
    $files = array();
    $join_contents = "";
    foreach($this->children as $child)
    {
      if($child instanceof lmbJsRequireOnceMacroTag)
      {
        $file = $root_dir . '/' . $child->get('src');
        if(!is_file($file) || !realpath($file) || null === ($content = file_get_contents($file)))
        {
          if($child->getBool('safe'))
          {
            $join_contents .= "\n/* ".basename($file)." - NOT FOUND */\n";
            continue;
          }
          else
            throw new lmbMacroException('File "' . $file . '" not found in '.$root_dir.', src: "' . $child->get('src') . '"');
        }
        $file = lmbFs :: normalizePath(realpath($file));
        if(!in_array($file, $files))
        {
          $files[] = $file;
          $join_contents .= "\n/* ".basename($file)." */\n" . $content;
        }
      }
      else
        $child->generate($code);
    }
    $url = lmbFs :: normalizePath(ltrim($this->get('dir') . '/' . md5(implode("\n", $files)).'.js', '/'));
    lmbFs :: safeWrite($root_dir . '/' . $url, trim($join_contents));
    $code->writeHTML('<script type="text/javascript" src="' . lmbToolkit :: instance()->addVersionToUrl($url) . '"></script>'); 
  }
}
