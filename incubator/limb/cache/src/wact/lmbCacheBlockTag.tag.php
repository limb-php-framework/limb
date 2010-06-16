<?php
/**
 * @tag cache:block
 * @req_attributes ttl key group
 *
 */
class lmbCacheBlockTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
		
  		$code -> writePHP('$toolkit = lmbToolkit::instance();');
  		$code -> writePHP('$cache = $toolkit->getCache();');
  		
  		$code->writePHP('$cache_key = ');
  		$code->writePHP($this->attributeNodes['key']->generateExpression($code));
  		$code->writePHP(';');
  		$code->writePHP('if ($cached = $cache->get($cache_key, array("group" => "'.$this->attributeNodes['group']->getValue().'", "raw" => 1))) {');
  		
  		$code->writePHP('echo $cached;');
  		//$code->writePHP('echo "cached output";');
  		$code->writePHP('} else {');
  		
  		$code->writePHP('ob_start();');
    	parent :: generateTagContent($code);
    	$code->writePHP('$cache->set($cache_key, ob_get_flush(), array("ttl" => "'.$this->attributeNodes['ttl']->getValue().'", "group" => "'.$this->attributeNodes['group']->getValue().'", "raw" => 1));');
    	//$code->writePHP('echo "stored in cache";');

	    $code->writePHP('}');
  }
}
