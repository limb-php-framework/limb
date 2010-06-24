<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/imagekit/src/lmbAbstractImageContainer.class.php');
lmb_require('limb/imagekit/src/exception/lmbImageTypeNotSupportedException.class.php');
lmb_require('limb/imagekit/src/exception/lmbImageCreateFailedException.class.php');
lmb_require('limb/imagekit/src/exception/lmbImageSaveFailedException.class.php');
lmb_require('limb/fs/src/exception/lmbFileNotFoundException.class.php');

/**
 * Imagick image container
 *
 * @package imagekit
 * @version $Id$
 */
class lmbImImageContainer extends lmbAbstractImageContainer
{

  protected static $supported_types = array('GIF', 'JPEG', 'PNG', 'WBMP', 'gif', 'jpeg', 'png', 'wbmp');

  /**
   * @var Imagick
   */
  protected $img;
  protected $img_type;
  protected $pallete;
  protected $out_type;

  function setOutputType($type)
  {
    if($type)
    {
      if(!self::supportSaveType($type))
      {
        throw new lmbImageTypeNotSupportedException($type);
      }
      $this->out_type = $type;
    }

    parent::setOutputType($type);
  }

  function load($file_name, $type = '')
  {
    $this->destroyImage();

    $imginfo = @getimagesize($file_name);
    if(!$imginfo)
      throw new lmbFileNotFoundException($file_name);


    $this->img = new Imagick();
    $this->img->readImage($file_name);
    if (!($this->img instanceof Imagick))
      throw new lmbImageCreateFailedException($file_name);

    $this->img_type = $this->img->getImageFormat();
  }

  function save($file_name = null, $quality = null)
  {
    $type = $this->out_type;
    if(!$type)
      $type = $this->img_type;

    if(!self::supportSaveType($type))
      throw new lmbImageTypeNotSupportedException($type);

    $this->img->setImageFormat($type);
    $this->img->setImageFilename($file_name);

    if(!is_null($quality) && strtolower($type) == 'jpeg')
    {
    	if(method_exists($this->img, 'setImageCompression'))
    	{
    		$this->img->setImageCompression(imagick::COMPRESSION_JPEG);
        $this->img->setImageCompressionQuality($quality);
    	}
    	else
    	{
        $this->img->setCompression(imagick::COMPRESSION_JPEG);
        $this->img->setCompressionQuality($quality);
    	}
    }

    if (!$this->img->writeImage($file_name))
      throw new lmbImageSaveFailedException($file_name);

    $this->destroyImage();
  }

  function getResource()
  {
    return $this->img;
  }

  function replaceResource($img)
  {
    $this->destroyImage();
    $this->img = $img;
  }

  function isPallete()
  {
    return ($this->img->getImageColors() < 256);
  }

  function getWidth()
  {
    return $this->img->getImageWidth();
  }

  function getHeight()
  {
    return $this->img->getImageHeight();
  }

  function destroyImage()
  {
    if(!$this->img)
      return;
    $this->img = null;
  }



  static function supportLoadType($type)
  {
    return self::supportType($type);
  }

  static function supportSaveType($type)
  {
    return self::supportType($type);
  }

  static function supportType($type)
  {
    if(!class_exists('Imagick'))
      return false;
    return (boolean)(in_array($type, self::$supported_types));
  }

  static function convertImageType($type)
  {
    switch ($type)
    {
      case 2:
        return "JPEG";
      break;
      case 3:
        return "PNG";
      break;
      case 1:
        return "GIF";
      break;
      case 15:
        return "WBMP";
      break;
    }
  }

  function __destruct()
  {
    $this->destroyImage();
  }
}

