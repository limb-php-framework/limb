<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbInsertQuery.class.php');
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');
lmb_require('limb/dbal/src/criteria/lmbSQLFieldCriteria.class.php');
lmb_require('limb/dbal/src/query/lmbUpdateQuery.class.php');

/**
 * class lmbFullTextSearchIndexer.
 *
 * @package search
 * @version $Id: lmbFullTextSearchIndexer.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class lmbFullTextSearchIndexer
{
  protected $normalizer = null;

  protected $left_bound = '<!-- no index start -->';
  protected $right_bound = '<!-- no index end -->';
  protected $use_noindex = false;

  protected $conn;

  function __construct($normalizer)
  {
    $this->normalizer = $normalizer;
    $this->conn = lmbToolkit :: instance()->getDefaultDbConnection();
  }

  function useNOINDEX($status = true)
  {
    $this->use_noindex = $status;
  }

  function index($uri, $content)
  {
    $title = $this->_extractTitle($content);
    $content = $this->_getIndexedContent($content);

    $content = $this->normalizer->process($content);

    if($record = $this->findIndexRecordByUri($uri))
    {
      $this->_updateIndexRecordById($record->get('id'), $content, $title);
      return $record->get('id');
    }
    else
      return $this->_insertNewIndexRecord($uri, $content, $title);
  }

  function _getIndexedContent($content)
  {
    if(!$this->use_noindex)
      return $content;

    $regex = '~' .
             preg_quote($this->left_bound) .
             '(.*?)' .
             preg_quote($this->right_bound) .
             '~s';

    return preg_replace($regex, ' ', $content);
  }

  function _extractTitle(&$content)
  {
    $regex = '~<title>([^<]*)</title>~';
    if(preg_match($regex, $content, $matches))
      return $matches[1];
    else
      return '';
  }

  function _insertNewIndexRecord($uri, $content, $title)
  {
    $query = new lmbInsertQuery(FULL_TEXT_SEARCH_INDEXER_TABLE, $this->conn);
    $query->addField('uri', $uri->toString());
    $query->addField('content', $content);
    $query->addField('last_modified', time());
    $query->addField('title', $title);
    $stmt = $query->getStatement();
    return $stmt->insertId('id');
  }

  function _updateIndexRecordById($id, $content, $title)
  {
    $query = new lmbUpdateQuery(FULL_TEXT_SEARCH_INDEXER_TABLE, $this->conn);
    $query->addField('content', $content);
    $query->addField('last_modified', time());
    $query->addField('title', $title);
    $query->addCriteria(new lmbSQLFieldCriteria('id', $id));
    $stmt = $query->getStatement();
    $stmt->execute();
  }

  function findIndexRecordByUri($uri)
  {
    $query = new lmbSelectQuery(FULL_TEXT_SEARCH_INDEXER_TABLE, $this->conn);
    $query->addCriteria(new lmbSQLFieldCriteria('uri', $uri->toString()));
    $rs = $query->getRecordSet();
    $rs->rewind();
    if($rs->valid())
      return $rs->current();
  }
}


