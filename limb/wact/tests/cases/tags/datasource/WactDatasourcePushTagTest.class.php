<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactDatasourcePushTagTest extends WactTemplateTestCase
{
  function testPush()
  {
    $template = '<datasource:push from="{$#names}" to="[fathers]"/><core:datasource id="fathers">{$name}</core:datasource>';

    $this->registerTestingTemplate('/tags/datasource/datasource_push.html', $template);

    $page = $this->initTemplate('/tags/datasource/datasource_push.html');

    $page->set('names', array('name'=> 'joe'));

    $this->assertEqual($page->capture(), 'joe');
  }

  function testPushWithTargetAttribute()
  {
    $template = '<datasource:push from="{$#names}" target="fathers"/><core:datasource id="fathers">{$name}</core:datasource>';

    $this->registerTestingTemplate('/tags/datasource/datasource_push_with_target_attribute.html', $template);

    $page = $this->initTemplate('/tags/datasource/datasource_push_with_target_attribute.html');

    $page->set('names', array('name'=> 'joe'));

    $this->assertEqual($page->capture(), 'joe');
  }
}

