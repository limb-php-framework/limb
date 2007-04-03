<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: templatecompiler.inc.php 5168 2007-02-28 16:05:08Z serega $
 * @package    wact
 */

/**
* Include all the compile time base components plus the compiler
*/
require_once 'limb/wact/src/compiler/WactDictionaryHolder.class.php';

require_once 'limb/wact/src/compiler/expression/WactExpressionInterface.interface.php';

require_once 'limb/wact/src/compiler/WactCompiler.class.php';

require_once 'limb/wact/src/compiler/compile_tree_node/WactCompileTreeNode.class.php';
require_once 'limb/wact/src/compiler/compile_tree_node/WactCompileTreeRootNode.class.php';
require_once 'limb/wact/src/compiler/compile_tree_node/WactTextNode.class.php';
require_once 'limb/wact/src/compiler/compile_tree_node/WactPHPNode.class.php';
require_once 'limb/wact/src/compiler/compile_tree_node/WactOutputExpressionNode.class.php';

require_once 'limb/wact/src/compiler/tag_node/WactCompilerTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactRuntimeComponentTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactSilentCompilerTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactRuntimeComponentHTMLTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactRuntimeDatasourceComponentTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactRuntimeDatasourceComponentHTMLTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactGenericHTMLTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactGenericContainerHTMLTag.class.php';
require_once 'limb/wact/src/compiler/tag_node/WactTagInfoExtractor.class.php';

require_once 'limb/wact/src/compiler/attribute/WactAttributeNode.class.php';
require_once 'limb/wact/src/compiler/filter/WactCompilerFilter.class.php';

require_once 'limb/wact/src/compiler/expression/WactDataBindingExpression.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpression.class.php';

require_once 'limb/wact/src/compiler/property/WactCompilerProperty.class.php';
require_once 'limb/wact/src/compiler/property/WactConstantProperty.class.php';

require_once 'limb/wact/src/compiler/parser/WactParserListener.interface.php';
require_once 'limb/wact/src/compiler/parser/WactSourceLocation.class.php';
require_once('limb/wact/src/compiler/parser/WactNodeBuilder.class.php');
require_once('limb/wact/src/compiler/parser/WactTreeBuilder.class.php');
require_once('limb/wact/src/compiler/parser/WactLiteralParsingState.class.php');
require_once('limb/wact/src/compiler/parser/WactComponentParsingState.class.php');
require_once('limb/wact/src/compiler/parser/WactHTMLParser.class.php');
require_once 'limb/wact/src/compiler/parser/WactSourceFileParser.class.php';

require_once 'limb/wact/src/compiler/WactCodeWriter.class.php';
?>