<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactTemplateStatsFilterTestCase extends WactTemplateTestCase {

  function testStatsFormatFilterSum()
  {
    $template = '<list:list id="List"><list:item>{$val|stats:"ttl"} </list:item></list:list>Total:{$bogus|stats:"ttl","sum"}';

    $this->registerTestingTemplate('/template/filter/stats_filter_sum.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_sum.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, '10 20 30 Total:60');
  }

  function testStatsFormatFilterRunningSum()
  {
    $template  = '<list:list id="List"><list:item>';
    $template .= '{$val|stats:"rttl"} {$val|stats:"rttl", "sum"}'."\n";
    $template .= '</list:item></list:list>';

    $this->registerTestingTemplate('/template/filter/stats_filter_runsum.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_runsum.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, "10 10\n20 30\n30 60\n");
  }

  function testStatsFormatFilterCount()
  {
    $template  = '<list:list id="List"><list:item>';
    $template .= '{$val|stats:"cnt"} {$val|stats:"cnt", "count"}'."\n";
    $template .= '</list:item></list:list>';

    $this->registerTestingTemplate('/template/filter/stats_filter_cnt.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_cnt.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, "10 1\n20 2\n30 3\n");
  }

  function testStatsFormatFilterAvg()
  {
    $template = '<list:list id="List"><list:item>{$val|stats:"avg"} </list:item></list:list>Average:{$bogus|stats:"avg","avg"}';

    $this->registerTestingTemplate('/template/filter/stats_filter_avg.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_avg.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, '10 20 30 Average:20');
  }

  function testStatsFormatFilterSd()
  {
    $template = '<list:list id="List"><list:item>{$val|stats:"sd"} </list:item></list:list>Std. Dev.:{$bogus|stats:"sd","stdev"}';

    $this->registerTestingTemplate('/template/filter/stats_filter_sd.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_sd.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, '10 20 30 Std. Dev.:10');
  }

  function testStatsFormatFilterSdp()
  {
    $template = '<list:list id="List"><list:item>{$val|stats:"sdp"} </list:item></list:list>Std. Dev.:{$bogus|stats:"sdp","stdevp"|number:3}';

    $this->registerTestingTemplate('/template/filter/stats_filter_sdp.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_sdp.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, '10 20 30 Std. Dev.:8.165');
  }

  function testStatsFormatFilterAccumulateQuite()
  {
    $template = '<list:list id="List"><list:item>{$val|stats:"aq","accq"}</list:item></list:list>Total:{$bogus|stats:"aq","sum"}';

    $this->registerTestingTemplate('/template/filter/stats_filter_aq.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_aq.html');
    $list = $page->GetChild('List');
    $list->registerDataSet(new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30))));

    $output = $page->capture();
    $this->assertEqual($output, 'Total:60');
  }

  function testStatsFormatFilterAccumulateQuite2()
  {
    $template = '<list:list from="data"><list:item>{$val|stats:"aq2","accq"}</list:item></list:list>'
      .'<list:list from="data"><list:item>{$val} of {$val|stats:"aq2","sum"}</list:item></list:list>';

    $this->registerTestingTemplate('/template/filter/stats_filter_aq2.html', $template);
    $page = $this->initTemplate('/template/filter/stats_filter_aq2.html');

    $DS = new ArrayObject(array());
    $DS['data'] = new WactArrayIterator(array(array('val'=>10),array('val'=>20),array('val'=>30)));

    $page->registerDataSource($DS);

    $output = $page->capture();
    $this->assertEqual($output, '10 of 6020 of 6030 of 60');
  }
}

