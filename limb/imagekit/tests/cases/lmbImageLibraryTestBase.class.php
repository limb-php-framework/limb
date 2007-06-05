<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

abstract class lmbImageLibraryTestBase extends UnitTestCase
{
  var $library = null;
  var $input_file = '';
  var $output_file = '';

  function setUp()
  {
    $this->input_file = dirname(__FILE__) . '/images/input.jpg';
    $this->output_file = LIMB_VAR_DIR . '/output.jpg';

    if(!file_exists($this->output_file))
      touch($this->output_file);

    $input_type = 'jpeg';
    $output_type = 'jpeg';
    $this->library->setInputFile($this->input_file, $input_type);
    $this->library->setOutputFile($this->output_file, $output_type);
  }

  function tearDown()
  {
    if(file_exists($this->output_file))
      unlink($this->output_file);
  }

  function testInstalled()
  {
    $this->assertTrue($this->library->isLibraryInstalled());
  }

  function testResizeByMaxDimension()
  {
    $max_dimension = 200;
    $params = array('max_dimension' => $max_dimension);
    $this->library->resize($params);
    $this->library->commit();

    $info1 = getimagesize($this->input_file);
    $info2 = getimagesize($this->output_file);
    if ($info1[0] > $info1[1])
      $this->assertEqual($info2[0], $max_dimension);
    else
      $this->assertEqual($info2[1], $max_dimension);
  }

  function testResizeByScaleFactor()
  {
    $scale_factor = 2;
    $params = array('scale_factor' => $scale_factor);
    $this->library->resize($params);
    $this->library->commit();

    $info1 = getimagesize($this->input_file);
    $info2 = getimagesize($this->output_file);
    $this->assertEqual(floor($info1[0] * $scale_factor), $info2[0]);
    $this->assertEqual(floor($info1[1] * $scale_factor), $info2[1]);
  }

  function testResizeByXyScale()
  {
    $info1 = getimagesize($this->input_file);
    $xscale = 2;
    $yscale = 1.5;

    $params = array('xscale' => $xscale, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual(floor($info1[0] * $xscale), $info2[0]);
    $this->assertEqual(floor($info1[1] * $xscale), $info2[1]);

    $params = array('yscale' => $yscale, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual(floor($info1[0] * $yscale), $info2[0]);
    $this->assertEqual(floor($info1[1] * $yscale), $info2[1]);

    $params = array('xscale' => $xscale, 'yscale' => $yscale);
    $this->library->resize($params);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual(floor($info1[0] * $xscale), $info2[0]);
    $this->assertEqual(floor($info1[1] * $yscale), $info2[1]);
  }

  function testResizeByWidthHeight()
  {
    $info1 = getimagesize($this->input_file);
    $width = 200;
    $height = 300;

    $params = array('width' => $width, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info2[0], $width);
    $this->assertEqual($info2[1], floor($info1[1] * $width / $info1[0]));

    $params = array('height' => $height, 'preserve_aspect_ratio' => true);
    $this->library->resize($params);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info2[0], floor($info1[0] * $height / $info1[1]));
    $this->assertEqual($info2[1], $height);

    $params = array('width' => $width, 'height' => $height);
    $this->library->resize($params);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info2[0], $width);
    $this->assertEqual($info2[1], $height);
  }

/*  function test_rotate()
  {
    return;
    $angle = 30;

    $this->library->rotate($angle, '000000');
    $this->library->commit();

    $this->assertEqual(filesize($this->output_file), $this->rotated_size);
    clearstatcache();
  }
*/

  function testFlip()
  {
    $info1 = getimagesize($this->input_file);

    $this->library->flip(LIMB_IMAGE_LIBRARY_FLIP_HORIZONTAL);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info1[0], $info2[0]);
    $this->assertEqual($info1[1], $info2[1]);
//      $this->assertEqual(filesize($this->output_file), $this->hflipped_size);
    clearstatcache();

    $this->library->flip(LIMB_IMAGE_LIBRARY_FLIP_VERTICAL);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info1[0], $info2[0]);
    $this->assertEqual($info1[1], $info2[1]);
//      $this->assertEqual(filesize($this->output_file), $this->wflipped_size);
    clearstatcache();
  }

  function testCutInside()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = 10;
    $y = 10;
    $w = 50;
    $h = 50;

    //$this->library->rotate(30, $bgcolor);
    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);

  if(!file_exists($this->output_file))
    echo $this->output_file . '<br>';

    $this->assertEqual($info2[0], $w);
    $this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size1);
    clearstatcache();
  }

  function testCutOutside()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = -10;
    $y = -10;
    $w = 200;
    $h = 200;

    //$this->library->rotate(30, $bgcolor);
    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info2[0], $w);
    $this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size2);
    clearstatcache();
  }

  function testCutLeftUp()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = -10;
    $y = -10;
    $w = 50;
    $h = 50;

    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info2[0], $w);
    $this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size3);
    clearstatcache();
  }

  function testCutRightDown()
  {
    $info1 = getimagesize($this->input_file);
    $bgcolor = '000000';

    $x = 50;
    $y = 50;
    $w = 100;
    $h = 100;

    $this->library->cut($x, $y, $w, $h, $bgcolor);
    $this->library->commit();

    $info2 = getimagesize($this->output_file);
    $this->assertEqual($info2[0], $w);
    $this->assertEqual($info2[1], $h);
//      $this->assertEqual(filesize($this->output_file), $this->cutted_size4);
    clearstatcache();
  }
}
?>