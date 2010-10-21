<?php

lmb_require('limb/net/src/lmbInputStreamParser.class.php');

class lmbInputStreamParserTest extends UnitTestCase
{
  function testParse()
  {
    $dsn = lmb_var_dir() . '/input_stream';

    $params = 'foo_1=bar_1&foo_2=bar_2';
    file_put_contents($dsn, $params);

    $input_stream_parser= new lmbInputStreamParser();
    $parsed_params = $input_stream_parser->parse($dsn);

    $this->assertEqual($parsed_params['foo_1'], 'bar_1');
    $this->assertEqual($parsed_params['foo_2'], 'bar_2');
  }
}