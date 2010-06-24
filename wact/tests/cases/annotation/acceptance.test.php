<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/annotation/WactClassAnnotationParser.class.php');
require_once(dirname(__FILE__) . '/ListenerStub.class.php');

class AcceptanceTestOfWactClassAnnotationParser extends UnitTestCase {

    function testBaseFunctionality() {
        $fileContent = <<<EOD
<?php
//--------------------------------------------------------------------------------
// Copyright 2004 Procata, Inc.
// Released under the LGPL license (http://www.gnu.org/copyleft/lesser.html)
//--------------------------------------------------------------------------------
/**
 * @package wact
 */
//-------------------------------------------------------------------------------

/**
 * Bla-bla-bla
 * @object Article
 * @see Article.html
 * @access protected
 */
class Article extends DomainObject {
    /*
    * @private
    * @column title
    */
     var \$Title;

    /*
    * @private
    * @column body
    */
     var \$Body;

    /*
    * @property-getter isNew
    */
     function getIsNew() {
         return (time() - \$this->get('Created') < 10 * 60 * 60 * 24);
     }
}
?>
EOD;
        $listener = new ListenerStub;
        $tokenizer = new WactClassAnnotationParser();

        $tokenizer->process($listener, $fileContent);

        $this->assertEqual($listener->history,
                           array(array('annotation', 'package', 'wact'),
                                 array('annotation', 'object', 'Article'),
                                 array('annotation', 'see', 'Article.html'),
                                 array('annotation', 'access', 'protected'),
                                 array('beginClass', 'Article', 'DomainObject'),
                                 array('annotation', 'private', NULL),
                                 array('annotation', 'column', 'title'),
                                 array('property',   'Title', 'public'),
                                 array('annotation', 'private', NULL),
                                 array('annotation', 'column', 'body'),
                                 array('property',   'Body', 'public'),
                                 array('annotation', 'property-getter', 'isNew'),
                                 array('method',     'getIsNew'),
                                 array('endClass')));
    }
}


