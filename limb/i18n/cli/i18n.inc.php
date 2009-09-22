<?php

lmb_require('limb/i18n/src/scanner/lmbTSDocument.class.php');
lmb_require('limb/i18n/src/scanner/lmbI18nScanner.class.php');



function _i18n_taskman_err($message)
{
  taskman_sysmsg( "\n***ERROR***\n\n{$message}\n\n");
  exit;
}

/**
 *
 * @desc scanning folder for {{i18n and {{__ tags and write messages into .ts file
 * @param path_to_ts_file folder1 [ folder2 folder3 ... ]
 */
function  task_scan($args = array())
{
  if(count($args)<2)
  {
    _i18n_taskman_err("usage: " . basename(__FILE__) . " folder/for/scan path/to/ts/file.ts folder_for_scan1 folder2 fold3 ...");
  }
  
 
  
  $ts_file = array_shift($args);

  $scanner = new lmbI18nScanner($args);
  $scanner->scan();
  $scanner->searchMessages();
  _exclude_existing_messages($ts_file,$scanner);
  _write_new_messages_in_ts_file($ts_file, $scanner);
}


function _exclude_existing_messages($file,$scanner)
{
  $doc = new DOMDocument;
  $doc->preserveWhiteSpace = false;
  $error = !$doc->load($file,LIBXML_NONET );
  if($error)
  {
    _i18n_taskman_err("can't load XML-data from ".$file);
  }
  $xpath = new DOMXPath($doc);
  $entries = $xpath->query('//TS/context/message/source');

  foreach ($entries as $entry)
  {
    $scanner->deleteMessage($entry->nodeValue);
  }
}

function _write_new_messages_in_ts_file($file,$scanner)
{
  
  $doc = new lmbTSDocument;
  $doc->preserveWhiteSpace = false;
  $error = !$doc->load($file,LIBXML_NONET );
  if($error)
  {
    _i18n_taskman_err("can't load XML-data from ".$file);
  }
  foreach($scanner->getMessages() as $message)
  {
    $doc->addMessage($message);
  }

  if(false === $doc->save($file))
  {
    _i18n_taskman_err("can't write in file: {$file}");
  }
}

