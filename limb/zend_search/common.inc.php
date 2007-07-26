<?php
lmb_require('limb/i18n/utf8.inc.php');
set_include_path(get_include_path() . dirname(__FILE__).'/lib/');
require_once('Zend/Search/Lucene.php');
@define('ZEND_SEARCH_ENCODING','utf-8');
Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());
?>