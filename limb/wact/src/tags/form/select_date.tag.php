<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select_date.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/tags/form/select.tag.php';
require_once 'limb/wact/src/tags/core/block.tag.php';

/**
 * Compile time component for building runtime select date components
 * @tag form:selectyear
 * @runat_as WactFormTag
 * @restrict_self_nesting
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectYearTag extends SelectTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_date.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectDateComponent';

  /**
   * @var object
   */
  protected $selectYearObjectRefCode;

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
    $code_writer->writePHP('{ echo "[Year]"; } else { echo "_Year"; }');
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
   * @access protected
   */
  function generateContents($code_writer)
  {
    $code_writer->writePHP('$'.$this->selectYearObjectRefCode.'->renderContents();');
  }
}

//--------------------------------------------------------------------------------

/**
 * Compile time component for building runtime select date components
 * @tag form:WactSelectMonth
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectMonthTag extends SelectTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_date.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectDateComponent';

  /**
   * @var object
   */
  protected $selectMonthObjectRefCode;

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
    $code_writer->writePHP('{ echo "[Month]"; } else { echo "_Month"; }');
    $code_writer->writeHTML('"');
    $this->generateAttributeList($code_writer, array('name', 'groupName', 'asArray', 'format'));
    $code_writer->writeHTML('>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   * @access protected
   */
  function postGenerate($code_writer) {
    $code_writer->writeHTML('</select>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   * @access protected
   */
  function generateContents($code_writer)
  {
    $format = ($this->hasAttribute('format') ? $this->getAttribute('format') : 'long');
    $code_writer->writePHP('$'.$this->selectMonthObjectRefCode.'->setFormat("'.$format.'");');

    $code_writer->writePHP('$'.$this->selectMonthObjectRefCode.'->fillChoices();');
    $code_writer->writePHP('$'.$this->selectMonthObjectRefCode.'->renderContents();');
  }
}

//--------------------------------------------------------------------------------

/**
 * Compile time component for building runtime select date components
 * @tag form:selectday
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectDayTag extends SelectTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   * @access private
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_date.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectDateComponent';

  /**
   * @var object
   */
  protected $selectDayObjectRefCode;

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
    $code_writer->writePHP('{ echo "[Day]"; } else { echo "_Day"; }');
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
  function generateContents($code_writer)
  {
    $code_writer->writePHP('$'.$this->selectDayObjectRefCode.'->renderContents();');
  }
}

//--------------------------------------------------------------------------------

/**
 * Compile time component for building runtime select date components
 * @tag form:selectdate
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactSelectDateTag extends CoreBlockTag  //WactControlTag
{
  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/select_date.inc.php';

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName = 'WactFormSelectDateComponent';

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateYear($code_writer)
  {
    $selectYearObjectRefCode  = self :: generateNewServerId();
    $SelectYear = $this->findChildByClass('WactSelectYearTag');
    $SelectYear->selComponent = $this->getComponentRefCode();
    $SelectYear->selectYearObjectRefCode = $selectYearObjectRefCode;

    $SelectYear->setAttribute('groupName', $this->getAttribute('name'));

    $code_writer->writePHP($this->getComponentRefCode() . '->prepareYear();');

    $code_writer->writePHP('$'. $selectYearObjectRefCode . '='.
    $this->getComponentRefCode() . '->getYear();');

    $SelectYear->preGenerate($code_writer);
    $SelectYear->generateContents($code_writer);
    $SelectYear->postGenerate($code_writer);
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateMonth($code_writer)
  {
    $selectMonthObjectRefCode = self :: generateNewServerId();
    $SelectMonth = $this->findChildByClass('WactSelectMonthTag');
    $SelectMonth->selComponent = $this->getComponentRefCode();
    $SelectMonth->selectMonthObjectRefCode = $selectMonthObjectRefCode;

    $SelectMonth->setAttribute('groupName', $this->getAttribute('name'));

    $code_writer->writePHP($this->getComponentRefCode() . '->prepareMonth();');
    $code_writer->writePHP('$'. $selectMonthObjectRefCode . '='.
    $this->getComponentRefCode() . '->getMonth();');

    $SelectMonth->preGenerate($code_writer);
    $SelectMonth->generateContents($code_writer);
    $SelectMonth->postGenerate($code_writer);
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateDay($code_writer)
  {
    $selectDayObjectRefCode = self :: generateNewServerId();
    $SelectDay = $this->findChildByClass('WactSelectDayTag');
    $SelectDay->selComponent = $this->getComponentRefCode();
    $SelectDay->selectDayObjectRefCode = $selectDayObjectRefCode;

    $SelectDay->setAttribute('groupName', $this->getAttribute('name'));

    $code_writer->writePHP($this->getComponentRefCode() . '->prepareDay();');

    $code_writer->writePHP('$'. $selectDayObjectRefCode . '='.
    $this->getComponentRefCode() . '->getDay();');

    $SelectDay->preGenerate($code_writer);
    $SelectDay->generateContents($code_writer);
    $SelectDay->postGenerate($code_writer);
  }

  /**
   * @param WactCodeWriter
   * @return void
   */
  function generateContents($code_writer)
  {
    $functionMap = array(
      'selectyeartag'  => 'generateYear',
      'selectmonthtag' => 'generateMonth',
      'selectdaytag'   => 'generateDay'
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