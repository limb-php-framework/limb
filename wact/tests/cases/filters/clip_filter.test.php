<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactTemplateClipFilterTestCase extends WactTemplateTestCase
{
  function testSimpleClipVar()
  {
    $template = '{$val|clip:5}';

    $this->registerTestingTemplate('/template/filter/clip/clipvar.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipvar.html');
    $page->set('val', 'abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde');
  }

  function testSimpleClipLiteral()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:5}';

    $this->registerTestingTemplate('/template/filter/clip/clipstr.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipstr.html');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde');
  }

  function testSimpleClipVarStart()
  {
    $template = '{$val|clip:5,2}';

    $this->registerTestingTemplate('/template/filter/clip/clipvarstart.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipvarstart.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, 'cdefg');
  }

  function testSimpleClipLiteralStart()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:5,2}';

    $this->registerTestingTemplate('/template/filter/clip/clipstrstart.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipstrstart.html');

    $output = $page->capture();
    $this->assertEqual($output, 'cdefg');
  }

  function testSimpleClipVarSuffix()
  {
    $template = '{$val|clip:5,0,"..."} {$val|clip:12,0,"..."}';

    $this->registerTestingTemplate('/template/filter/clip/clipvarsuf.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipvarsuf.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde... abcdefgh');
  }

  function testSimpleClipLiteralSuffix()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:5,0,"..."} {$str|clip:12,0,"..."}';

    $this->registerTestingTemplate('/template/filter/clip/clipstrsuf.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipstrsuf.html');

    $output = $page->capture();
    $this->assertEqual($output, 'abcde... abcdefgh');
  }

  function testSimpleClipLiteralLenVarSuffix()
  {
    $template = '<core:set str="abcdefgh" />{$str|clip:len,0,"..."} {$str|clip:len2,0,"..."}';

    $this->registerTestingTemplate('/template/filter/clip/clipstrsuflenvar.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipstrsuflenvar.html');
    $page->set('len', 5);
    $page->set('len2', 12);

    $output = $page->capture();
    $this->assertEqual($output, 'abcde... abcdefgh');
  }

  function testLongStringWordBoundary()
  {
    $template = '{$val|clip:35,0,"...","n"} {$val|clip:35,0,"...","y"}';

    $this->registerTestingTemplate('/template/filter/clip/clipvarwordbound.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipvarwordbound.html');
    $page->set('val','Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In auctor sem vitae ante.');

    $output = $page->capture();
    $this->assertEqual($output, 'Lorem ipsum dolor sit amet, consect... Lorem ipsum dolor sit amet, consectetuer...');
  }

  function testLongLiteralStringWordBoundary()
  {
    $template = '<core:set str="Lorem ipsum dolor sit amet, consectetuer adipiscing elit. In auctor sem vitae ante." />'
        .'{$str|clip:35,0,"...","No"} {$str|clip:35,0,"...","Yes"}';

    $this->registerTestingTemplate('/template/filter/clip/clipstrwordbound.html', $template);
    $page = $this->initTemplate('/template/filter/clip/clipstrwordbound.html');

    $output = $page->capture();
    $this->assertEqual($output, 'Lorem ipsum dolor sit amet, consect... Lorem ipsum dolor sit amet, consectetuer...');
  }

  function testOneAttributeDoubleQuoteVar()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,"..."}"/>';

    $this->registerTestingTemplate('/template/filter/clip/testoneattributedoublequote.html', $template);

    try {
      $page = $this->initTemplate('/template/filter/clip/testoneattributedoublequote.html');
      $this->assertTrue(false);
    }
    catch (WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag attribute syntax/', $e->getMessage());
    }
  }

  function testOneAttributeSingleQuoteVar()
  {
    $template = '<img src=\'img.gif\' alt=\'{$val|clip:5,0,\'...\'}\'/>';

    $this->registerTestingTemplate('/template/filter/clip/testoneattributesinglequote.html',$template);
    try
    {
      $page = $this->initTemplate('/template/filter/clip/testoneattributesinglequote.html');
      $this->assertTrue(false);
    }
    catch (WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag attribute syntax/', $e->getMessage());
    }
  }

  function testOneAttributeMixedQuote1Var()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,\'...\'}"/>';

    $this->registerTestingTemplate('/template/filter/clip/testoneattributemixedquote1.html',$template);
    $page = $this->initTemplate('/template/filter/clip/testoneattributemixedquote1.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt="abcde..." />');
  }

  function testOneAttributeMixedQuote2Var()
  {
    $template = '<img src=\'img.gif\' alt=\'{'.'$val|clip:5,0,"..."}\'/>';

    $this->registerTestingTemplate('/template/filter/clip/testoneattributemixedquote2.html',$template);
    $page = $this->initTemplate('/template/filter/clip/testoneattributemixedquote2.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt=\'abcde...\' />');
  }

  function testTwoAttributeDoubleQuoteVar()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,"..."}" title="{$val|clip:5,0,"..."}"/>';

    $this->registerTestingTemplate('/template/filter/clip/testtwoattributedoublequote.html',$template);
    try
    {
      $page = $this->initTemplate('/template/filter/clip/testtwoattributedoublequote.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Invalid tag attribute syntax/', $e->getMessage());
    }
  }

  function testTwoAttributeMixedQuote1Var()
  {
    $template = '<img src="img.gif" alt="{$val|clip:5,0,\'...\'}" title="{$val|clip:5,0,\'...\'}"/>';

    $this->registerTestingTemplate('/template/filter/clip/testtwoattributemixedquote1.html',$template);
    $page = $this->initTemplate('/template/filter/clip/testtwoattributemixedquote1.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt="abcde..." title="abcde..." />');
  }

  function testTwoAttributeMixedQuote2Var()
  {
    $template = '<img src=\'img.gif\' alt=\'{'.'$val|clip:5,0,"..."}\' title=\''.'{$val|clip:5,0,"..."}\'/>';

    $this->registerTestingTemplate('/template/filter/clip/testtwoattributemixedquote2.html',$template);
    $page = $this->initTemplate('/template/filter/clip/testtwoattributemixedquote2.html');
    $page->set('val','abcdefgh');

    $output = $page->capture();
    $this->assertEqual($output, '<img src="img.gif" alt=\'abcde...\' title=\'abcde...\' />');
  }
}

