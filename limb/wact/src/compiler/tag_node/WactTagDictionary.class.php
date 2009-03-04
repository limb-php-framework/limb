<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/WactCompilerArtifactDictionary.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactTagInfo.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactTagInfoExtractor.class.php';

define('LOCATION_SERVER', 'server');
define('LOCATION_CLIENT', 'client');

/**
 * class WactTagDictionary.
 *
 * @package wact
 * @version $Id: WactTagDictionary.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactTagDictionary extends WactCompilerArtifactDictionary
{
  protected $info = array();

  function _createArtifactsExtractor($file)
  {
    return new WactTagInfoExtractor($this, $file);
  }

  function registerWactTagInfo($taginfo, $file)
  {
    $tag_to_lower = strtolower($taginfo->Tag);

    if(isset($this->info[$tag_to_lower]))
      return;

    $taginfo->File = $file;
    $this->info[$tag_to_lower] = $taginfo;
  }

  function getWactTagInfo($tag)
  {
    $tag = strtolower($tag);
    if (isset($this->info[$tag]))
      return $this->info[$tag];
  }

  /*
  * Determines whether a tag can be added to compiler tree as a node.
  * Examining attributes like wact:id, runat
  * @returns WactTagInfo
  */
  function findTagInfo($tag, $attrs, $isEmpty, $current_node)
  {
      $tag = strtolower($tag);
      if (isset($attrs['wact:id']))
        $attrs['runat'] = 'server';

      // Does the tag have the runat attribute? If so it might be a component
      if(isset($attrs['runat']))
      {
          if(strtolower($attrs['runat']) == 'server')
          {
              if(isset($this->info[$tag]))
                return $this->info[$tag];
              else
              {
                // we are a generic tag.  We run at the server, but have no
                // specific WactTagInfo record in the dictionary.
                if(!$isEmpty)
                {
                  $generic = new WactTagInfo($tag, 'WactGenericContainerHTMLTag');
                  $generic->File = 'limb/wact/src/compiler/tag_node/WactGenericContainerHTMLTag.class.php';
                }
                else
                {
                  $generic = new WactTagInfo($tag, 'WactGenericHTMLTag');
                  $generic->File = 'limb/wact/src/compiler/tag_node/WactGenericHTMLTag.class.php';
                  $generic->setForbidEndTag();
                }
                $generic->setRunat('client');

                return $generic;
              }
          }
      }
      else if (isset($this->info[$tag]))
      {
          $WactTagInfo = $this->info[$tag];

          if ($WactTagInfo->getRunat() == 'server')
              return $WactTagInfo;

          // Check if tag allowed to inherit runat attribute from parent
          if ($runat_as = $WactTagInfo->getRunatAs())
          {
            if ($parent = $current_node->findSelfOrParentByClass($runat_as))
            {
              if ($parent->getBoolAttribute('children_reuse_runat', TRUE))
                return $WactTagInfo;
            }
          }
      }
      return NULL;
  }
}

