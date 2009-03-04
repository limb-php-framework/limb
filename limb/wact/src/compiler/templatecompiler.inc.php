<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Include all the compile time base components plus the compiler
 *
 * @package wact
 * @version $Id: templatecompiler.inc.php 7686 2009-03-04 19:57:12Z korchasa $
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

require_once 'limb/wact/src/compiler/attribute/WactAttribute.class.php';
require_once 'limb/wact/src/compiler/attribute/WactAttributeLiteralFragment.class.php';
require_once 'limb/wact/src/compiler/attribute/WactAttributeExpressionFragment.class.php';

require_once 'limb/wact/src/compiler/filter/WactCompilerFilter.class.php';

require_once 'limb/wact/src/compiler/expression/WactExpression.class.php';

require_once 'limb/wact/src/compiler/property/WactCompilerProperty.class.php';
require_once 'limb/wact/src/compiler/property/WactConstantProperty.class.php';

require_once 'limb/wact/src/compiler/parser/WactHTMLParserListener.interface.php';
require_once('limb/wact/src/compiler/parser/WactHTMLParser.class.php');
require_once 'limb/wact/src/compiler/parser/WactSourceLocation.class.php';
require_once('limb/wact/src/compiler/parser/WactTreeBuilder.class.php');
require_once('limb/wact/src/compiler/parser/WactLiteralParsingState.class.php');
require_once('limb/wact/src/compiler/parser/WactComponentParsingState.class.php');
require_once 'limb/wact/src/compiler/parser/WactSourceFileParser.class.php';

require_once 'limb/wact/src/compiler/parser/WactBlockAnalizerListener.interface.php';
require_once 'limb/wact/src/compiler/parser/WactBlockAnalizer.class.php';
require_once 'limb/wact/src/compiler/parser/WactContentBlockAnalizerListener.class.php';
require_once 'limb/wact/src/compiler/parser/WactAttributeBlockAnalizerListener.class.php';

require_once 'limb/wact/src/compiler/WactCodeWriter.class.php';

