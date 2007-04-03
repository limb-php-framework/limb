<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select_time.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/tags/form/select.tag.php';
require_once 'limb/wact/src/tags/core/block.tag.php';

/**
 * Compile time component for building runtime select time components
 * @tag form:selecthour
 * @runat_as WactFormTag
 * @restrict_self_nesting
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectHourTag extends WactSelectTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_time.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectTimeComponent';

  /**
   * @var object
   */
  protected $SelectHourObjectRefCode;

  /**
   * @var object
   */
  protected $selComponent;

  /**
   * @param WactCodeWriter
   * @return void
   */
  function preGenerate($code_writer) {
    if ($this->hasAttribute('name')) {
      $this->removeAttribute('name');
    }
    $code_writer->writeHTML('<select name="');
    $code_writer->writePHP('echo '.$this->selComponent.'->groupName;');
    $code_writer->writePHP('if ('.$this->selComponent.'->asArray)');
    $code_writer->writePHP('{ echo "[Hour]"; } else { echo "_Hour"; }');
    $code_writer->writeHTML('"');
    $this->generateAttributeList($code_writer, array('name', 'groupName', 'asArray'));
    $code_writer->writeHTML('>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function postGenerate($code_writer) {
    $code_writer->writeHTML('</select>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateContents($code_writer) {
    $code_writer->writePHP('$'.$this->SelectHourObjectRefCode.'->renderContents();');
  }
}

//--------------------------------------------------------------------------------

/**
 * Compile time component for building runtime select time components
 * @tag form:selectminute
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectMinuteTag extends WactSelectTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   * @access private
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_time.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectTimeComponent';

  /**
   * @var object
   */
  protected $SelectMinuteObjectRefCode;

  /**
   * @var object
   */
  protected $selComponent;

  /**
   * @param WactCodeWriter
   * @return void
   */
  function preGenerate($code_writer)
  {
    if ($this->hasAttribute('name')) {
      $this->removeAttribute('name');
    }

    $code_writer->writeHTML('<select name="');
    $code_writer->writePHP('echo '.$this->selComponent.'->groupName;');
    $code_writer->writePHP('if ('.$this->selComponent.'->asArray)');
    $code_writer->writePHP('{ echo "[Minute]"; } else { echo "_Minute"; }');
    $code_writer->writeHTML('"');
    $this->generateAttributeList($code_writer, array('name', 'groupName', 'asArray'));
    $code_writer->writeHTML('>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function postGenerate($code_writer) {
    $code_writer->writeHTML('</select>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateContents($code_writer) {
    $code_writer->writePHP('$'.$this->SelectMinuteObjectRefCode.'->renderContents();');
  }
}

//--------------------------------------------------------------------------------

/**
 * Compile time component for building runtime select time components
 * @tag form:selectsecond
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectSecondTag extends WactSelectTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_time.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectTimeComponent';

  /**
   * @var object
   */
  protected $SelectSecondObjectRefCode;

  /**
   * @var object
   */
  protected $selComponent;

  /**
   * @param WactCodeWriter
   * @return void
   */
  function preGenerate($code_writer)
  {
    //discard
    if ($this->hasAttribute('name')) {
      $this->removeAttribute('name');
    }
    $code_writer->writeHTML('<select name="');
    $code_writer->writePHP('echo '.$this->selComponent.'->groupName;');
    $code_writer->writePHP('if ('.$this->selComponent.'->asArray)');
    $code_writer->writePHP('{ echo "[Second]"; } else { echo "_Second"; }');
    $code_writer->writeHTML('"');
    $this->generateAttributeList($code_writer, array('name', 'groupName'));
    $code_writer->writeHTML('>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function postGenerate($code_writer) {
    $code_writer->writeHTML('</select>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateContents($code_writer) {
    $code_writer->writePHP('$'.$this->SelectSecondObjectRefCode.'->renderContents();');
  }
}

//--------------------------------------------------------------------------------

/**
 * Compile time component for building runtime select time components
 * @tag form:selecttime
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectTimeTag extends WactCoreBlockTag  //ControlTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_time.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectTimeComponent';

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateHour($code_writer)
  {
    $SelectHourObjectRefCode  = self :: generateNewServerId();
    $SelectHour = $this->findChildByClass('WactSelectHourTag');
    $SelectHour->selComponent = $this->getComponentRefCode();
    $SelectHour->SelectHourObjectRefCode = $SelectHourObjectRefCode;

    $SelectHour->setAttribute('groupName', $this->getAttribute('name'));

    $code_writer->writePHP($this->getComponentRefCode() . '->prepareHour();');

    $code_writer->writePHP('$'. $SelectHourObjectRefCode . '='.
    $this->getComponentRefCode() . '->getHour();');

    $SelectHour->preGenerate($code_writer);
    $SelectHour->generateContents($code_writer);
    $SelectHour->postGenerate($code_writer);
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateMinute($code_writer)
  {
    $SelectMinuteObjectRefCode = self :: generateNewServerId();
    $SelectMinute = $this->findChildByClass('WactSelectMinuteTag');
    $SelectMinute->selComponent = $this->getComponentRefCode();
    $SelectMinute->SelectMinuteObjectRefCode = $SelectMinuteObjectRefCode;

    $SelectMinute->setAttribute('groupName', $this->getAttribute('name'));

    $code_writer->writePHP($this->getComponentRefCode() . '->prepareMinute();');
    $code_writer->writePHP('$'. $SelectMinuteObjectRefCode . '='.
    $this->getComponentRefCode() . '->getMinute();');

    $SelectMinute->preGenerate($code_writer);
    $SelectMinute->generateContents($code_writer);
    $SelectMinute->postGenerate($code_writer);
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateSecond($code_writer)
  {
    $SelectSecondObjectRefCode = self :: generateNewServerId();
    $SelectSecond = $this->findChildByClass('WactSelectSecondTag');
    $SelectSecond->selComponent = $this->getComponentRefCode();
    $SelectSecond->SelectSecondObjectRefCode = $SelectSecondObjectRefCode;

    $SelectSecond->setAttribute('groupName', $this->getAttribute('name'));

    $code_writer->writePHP($this->getComponentRefCode() . '->prepareSecond();');

    $code_writer->writePHP('$'. $SelectSecondObjectRefCode . '='.
    $this->getComponentRefCode() . '->getSecond();');

    $SelectSecond->preGenerate($code_writer);
    $SelectSecond->generateContents($code_writer);
    $SelectSecond->postGenerate($code_writer);
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateContents($code_writer)
  {
    $functionMap = array(
      'selecthourtag'   => 'generateHour',
      'selectminutetag' => 'generateMinute',
      'selectsecondtag' => 'generateSecond'
    );

    $code_writer->writePHP($this->getComponentRefCode() . '->setGroupName("'.$this->getAttribute('name').'");');
    $code_writer->writePHP($this->getComponentRefCode() . '->setAsArray();');

    foreach ($this->children as $key => $child) {
      $childClass = strtolower(get_class($child));
      if (in_array($childClass, array_keys($functionMap))) {
        $this->$functionMap[$childClass]($code_writer);
      } else {
        $this->children[$key]->generate($code_writer);
      }
    }
  }
}
?>
