<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/search/src/db/query/lmbMySQL4FullTextSearchQuery.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

/**
 * class lmbSearchFetcher.
 *
 * @package search
 * @version $Id: lmbSearchFetcher.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbSearchFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $query = new lmbMySQL4FullTextSearchQuery('full_text_uri_content_index',
                                              $this->_getQueryWords(),
                                              true,
                                              lmbToolkit :: instance()->getDefaultDbConnection());

    return $query->getRecordSet();
  }

  protected function _collectDecorators()
  {
    if($words = $this->_getQueryWords())
      $this->addDecorator('limb/search/src/dataset/lmbSearchResultProcessor',
                          array('words' => $words,
                                'matched_word_folding_radius' => 40,
                                'gaps_pattern' => '...',
                                'match_left_mark' => '<b>',
                                'match_right_mark' => '</b>',
                                'matching_lines_limit' => 4));
  }

  protected function _getQueryWords()
  {
    $request = lmbToolkit :: instance()->getRequest();
    $query = $request->get('query_string');
    return explode(' ', htmlspecialchars($query));
  }
}


