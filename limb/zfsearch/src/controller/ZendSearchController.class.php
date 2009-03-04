<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 /**
 * class ZendSearchController
 *
 * @package zfsearch
 * @version $Id$
 */
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/search/src/dataset/lmbSearchResultProcessor.class.php');

class ZendSearchController extends lmbController
{
  function doResult()
  {
    Zend_Search_Lucene_Search_QueryParser :: setDefaultEncoding(ZEND_SEARCH_ENCODING);
    $query = Zend_Search_Lucene_Search_QueryParser :: parse($this->_getQueryWords());
    $index = Zend_Search_Lucene :: open(LIMB_VAR_DIR . '/search_index');
    
    try
    {
      $hits = $index->find($query);
    }
    catch(Zend_Exception $e)
    {
      $hits = array();
    }
 
    $search_result = new lmbCollection();
    foreach($hits as $hit)
    {
      $doc = $hit->getDocument();
      $search_result[] = array('id' => $hit->id,
                        'score' => $hit->score,
                        'title' => $hit->title,
                        'uri' => $hit->uri,
                        'content' => $hit->body);
    }
    $this->query_string = $this->_getQueryWords();
    
    $this->result = new lmbSearchResultProcessor($search_result);
    $this->result->setMatchedWordFoldingRadius(70);
    $this->result->setGapsPattern('...');
    $this->result->setMatchLeftMark('<b>');
    $this->result->setMatchRightMark('</b>');
    $this->result->setWords(explode (' ', $this->query_string));
    $this->result->setMatchingLinesLimit(3); 
  }
 
  protected function _getQueryWords()
  {
    $query = lmb_strtolower($this->request->get('query_string'));
    return htmlspecialchars($query);
  }
}

