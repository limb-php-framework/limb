<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/search/src/indexer/lmbFullTextSearchIndexer.class.php');
lmb_require('limb/search/src/indexer/lmbSearchTextNormalizer.class.php');

Mock :: generate('lmbSearchTextNormalizer', 'MockSearchTextNormalizer');

class lmbFullTextSearchIndexerTest extends UnitTestCase
{
  protected $db;

  function setUp()
  {
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();
  }

  function _cleanUp()
  {
    $this->db->delete(FULL_TEXT_SEARCH_INDEXER_TABLE);
  }

  function testNormalizeContent()
  {
    $uri = new lmbUri('index.html');
    $content = 'content';
    $processed_content = 'new content';

    $normalizer = new MockSearchTextNormalizer($this);
    $indexer = new lmbFullTextSearchIndexer($normalizer);

    $normalizer->expectOnce('process', array($content));
    $normalizer->setReturnValue('process', $processed_content, array($content));

    $indexer->index($uri, $content);

    $rs = $this->db->select(FULL_TEXT_SEARCH_INDEXER_TABLE);
    $arr = $rs->getArray();

    $this->assertEqual($arr[0]['content'], $processed_content);
  }

  function testNOINDEX()
  {
    $uri = new lmbUri('index.html');

    $content = "must be indexed\n<!-- no index start -->ignored by indexer<!-- no index end -->must be indexed also";

    $expected = "must be indexed\n must be indexed also";

    $normalizer = new MockSearchTextNormalizer($this);
    $indexer = new lmbFullTextSearchIndexer($normalizer);
    $indexer->useNOINDEX();

    $normalizer->expectOnce('process', array($expected));
    $normalizer->setReturnValue('process', 'whatever');

    $indexer->index($uri, $content);
  }

  function testSwitchOffNOINDEX()
  {
    $uri = new lmbUri('index.html');

    $content = "must be indexed\n<!-- no index start -->ignored by indexer<!-- no index end -->must be indexed also";

    $expected = "must be indexed\n<!-- no index start -->ignored by indexer<!-- no index end -->must be indexed also";

    $normalizer = new MockSearchTextNormalizer($this);
    $indexer = new lmbFullTextSearchIndexer($normalizer);
    $indexer->useNOINDEX(false);

    $normalizer->expectOnce('process', array($expected));
    $normalizer->setReturnValue('process', 'whatever', array($expected));

    $indexer->index($uri, $content);
  }

  function testNOINDEXMultiline()
  {
    $uri = new lmbUri('index.html');

    $content = "\nmust be indexed\n\n<!-- no index start -->ignored by indexer\n<!-- no index end -->\n must be indexed also";

    $expected = "\nmust be indexed\n\n \n must be indexed also";

    $normalizer = new MockSearchTextNormalizer($this);
    $indexer = new lmbFullTextSearchIndexer($normalizer);
    $indexer->useNOINDEX();

    $normalizer->expectOnce('process', array($expected));
    $normalizer->setReturnValue('process', 'whatever');

    $indexer->index($uri, $content);
  }

  function testIndexNew()
  {
    $uri = new lmbUri('index.html');
    $content = '<title>test title</title>content';
    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri, $content);

    $rs = $this->db->select(FULL_TEXT_SEARCH_INDEXER_TABLE);
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['uri'], $uri->toString());
    $this->assertEqual($arr[0]['content'], 'test title content');
    $this->assertEqual($arr[0]['title'], 'test title');
    $this->assertTrue($arr[0]['last_modified'] > 0 && $arr[0]['last_modified'] <= time());
  }

  function testIndexNewUtf8Text()
  {
    $uri = new lmbUri('index.html');
    $content = '<title>Растения</title>Доставка';
    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri, $content);

    $rs = $this->db->select(FULL_TEXT_SEARCH_INDEXER_TABLE);
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['uri'], $uri->toString());
    $this->assertEqual($arr[0]['content'], 'растения доставка');
    $this->assertEqual($arr[0]['title'], 'Растения');
    $this->assertTrue($arr[0]['last_modified'] > 0 && $arr[0]['last_modified'] <= time());
  }

  function testExtractTitleAnyway()
  {
    $uri = new lmbUri('index.html');
    $content = '<!-- no index start --><title>test title</title>content ignored<!-- no index end -->content';

    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->useNOINDEX();
    $indexer->index($uri, $content);

    $rs = $this->db->select(FULL_TEXT_SEARCH_INDEXER_TABLE);
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['uri'], $uri->toString());
    $this->assertEqual($arr[0]['content'], 'content');
    $this->assertEqual($arr[0]['title'], 'test title');
    $this->assertTrue($arr[0]['last_modified'] > 0 && $arr[0]['last_modified'] <= time());
  }

  function testIndexUpdate()
  {
    $uri = new lmbUri('index.html');

    $this->db->insert(FULL_TEXT_SEARCH_INDEXER_TABLE,
                          array('uri' => $uri->toString(),
                                'content' => 'content1',
                                'title' => 'title1',
                                'last_modified' => $time1 = 200));

    $new_content = '<title>title2</title>content2';

    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri, $new_content);

    $rs = $this->db->select(FULL_TEXT_SEARCH_INDEXER_TABLE);
    $arr = $rs->getArray();

    $this->assertEqual(sizeof($arr), 1);
    $this->assertEqual($arr[0]['uri'], $uri->toString());
    $this->assertEqual($arr[0]['content'], 'title2 content2');
    $this->assertEqual($arr[0]['title'], 'title2');
    $this->assertTrue($arr[0]['last_modified'] > $time1 && $arr[0]['last_modified'] <= time());
  }

  function testFindIndexRecordByUri()
  {
    $uri1 = new lmbUri('index.html');
    $content1 = '<title>title1</title>content1';

    $indexer = new lmbFullTextSearchIndexer(new lmbSearchTextNormalizer());
    $indexer->index($uri1, $content1);

    $uri2 = new lmbUri('page1.html');
    $content2 = '<title>title2</title>content2';

    $indexer->index($uri2, $content2);

    $record = $indexer->findIndexRecordByUri($uri1)->export();
    $this->assertEqual($record['uri'], $uri1->toString());
    $this->assertEqual($record['content'], 'title1 content1');
    $this->assertEqual($record['title'], 'title1');

    $record = $indexer->findIndexRecordByUri($uri2)->export();
    $this->assertEqual($record['uri'], $uri2->toString());
    $this->assertEqual($record['content'], 'title2 content2');
    $this->assertEqual($record['title'], 'title2');
  }
}


