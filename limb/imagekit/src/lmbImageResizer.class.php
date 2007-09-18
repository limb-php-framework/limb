<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbImageFactory.class.php');
lmb_require('limb/net/src/lmbMimeType.class.php');
/**
 * class lmbImage
 *
 * @package imagekit
 * @version $Id$
 */
class lmbImageResizer
{
  protected $filename;
  protected $mime_type;

  function __construct($filepath, $mime_type = null)
  {
    $this->filepath = $filepath;

    if($mime_type)
      $this->mime_type = $mime_type;
    else
      $this->mime_type = lmbMimeType :: getFileMimeType($filepath);
  }

  function getFilePath()
  {
    return $this->filepath;
  }

  function getMimeType()
  {
    return $this->mime_type;
  }

  function resize($size)
  {
    $input_file = $this->getFilePath();
    $tmp_file = lmbFs :: generateTmpFile();

    try
    {
      $image_library = lmbImageFactory :: create();

      $input_file_type = $image_library->getImageType($this->getMimeType());
      $output_file_type = $image_library->fallBackToAnySupportedType($input_file_type);

      $output_file = $tmp_file . '.' . $output_file_type;

      $image_library->setInputFile($input_file);
      $image_library->setInputType($input_file_type);

      $image_library->setOutputFile($output_file);
      $image_library->setOutputType($output_file_type);

      if(!is_array($size))
        $image_library->resize(array('max_dimension' => $size));
      else
        $image_library->resize($size);

      $image_library->commit();
    }
    catch(lmbException $e)
    {
      if(file_exists($output_file))
        unlink($output_file);

      throw $e;
    }

    $this->filepath = $output_file;
    return $output_file;
  }

  function cleanup()
  {
    if(file_exists($this->filepath))
      unlink($this->filepath);
  }
}
?>
