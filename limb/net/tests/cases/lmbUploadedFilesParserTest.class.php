<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/net/src/lmbUploadedFilesParser.class.php');

class lmbUploadedFilesParserTest extends UnitTestCase
{
  var $parser;

  function setUp()
  {
    $this->parser = new lmbUploadedFilesParser();
  }

  function testEmpty()
  {
     $result = $this->parser->parse(array());
     $this->assertEqual($result, array());
  }

  function testSimple()
  {
     $files = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'file2' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $files);
  }

  function testObjectifySimple()
  {
     $files = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'file2' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
     );

     $expected = array('file1' => new lmbUploadedFile($files['file1']),
                       'file2' => new lmbUploadedFile($files['file2']));

     $result = $this->parser->objectify($files);
     $this->assertEqual($result, $expected);
  }

  function testComplex()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => 'file',
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => 'file_type',
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => 'file_tmp_name',
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => 'file_size',
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => 'file_err_code',
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
          'file2' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $expected);
  }

  function testObjectifyComplex()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => 'file',
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => 'file_type',
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => 'file_tmp_name',
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => 'file_size',
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => 'file_err_code',
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => new lmbUploadedFile(array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           )),
          'file2' => new lmbUploadedFile(array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           )),
         ),
     );

     $result = $this->parser->objectify($files);
     $this->assertEqual($result, $expected);
  }

  function testMegaComplex()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => array(
                                            '1' => 'file',
                                            '2' => 'file',
                                            ),
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => array(
                                            '1' => 'file_type',
                                            '2' => 'file_type',
                                            ),
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => array(
                                            '1' => 'file_tmp_name',
                                            '2' => 'file_tmp_name',
                                            ),
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => array(
                                            '1' => 'file_size',
                                            '2' => 'file_size',
                                            ),
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => array(
                                            '1' => 'file_err_code',
                                            '2' => 'file_err_code',
                                            ),
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => array(
            '1' => array(
               'name' => 'file',
               'type' => 'file_type',
               'tmp_name' => 'file_tmp_name',
               'size' => 'file_size',
               'error' => 'file_err_code'
             ),
            '2' => array(
               'name' => 'file',
               'type' => 'file_type',
               'tmp_name' => 'file_tmp_name',
               'size' => 'file_size',
               'error' => 'file_err_code'
             ),
          ),
          'file2' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $expected);
  }

  function testObjectifyMegaComplex()
  {
     $files = array(
        'form' => array(
           'name' => array(
                           'file1' => array(
                                            '1' => 'file',
                                            '2' => 'file',
                                            ),
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => array(
                                            '1' => 'file_type',
                                            '2' => 'file_type',
                                            ),
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => array(
                                            '1' => 'file_tmp_name',
                                            '2' => 'file_tmp_name',
                                            ),
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => array(
                                            '1' => 'file_size',
                                            '2' => 'file_size',
                                            ),
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => array(
                                            '1' => 'file_err_code',
                                            '2' => 'file_err_code',
                                            ),
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'form' => array(
          'file1' => array(
            '1' => new lmbUploadedFile(array(
               'name' => 'file',
               'type' => 'file_type',
               'tmp_name' => 'file_tmp_name',
               'size' => 'file_size',
               'error' => 'file_err_code'
             )),
            '2' => new lmbUploadedFile(array(
               'name' => 'file',
               'type' => 'file_type',
               'tmp_name' => 'file_tmp_name',
               'size' => 'file_size',
               'error' => 'file_err_code'
             )),
          ),
          'file2' => new lmbUploadedFile(array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           )),
         ),
     );

     $result = $this->parser->objectify($files);
     $this->assertEqual($result, $expected);
  }

  function testMixed()
  {
     $files = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'form' => array(
           'name' => array(
                           'file1' => 'file',
                           'file2' => 'file',
                           ),
           'type' => array(
                           'file1' => 'file_type',
                           'file2' => 'file_type',
                           ),
           'tmp_name' => array(
                               'file1' => 'file_tmp_name',
                               'file2' => 'file_tmp_name',
                               ),
           'size' => array(
                           'file1' => 'file_size',
                           'file2' => 'file_size',
                           ),
           'error' => array(
                           'file1' => 'file_err_code',
                           'file2' => 'file_err_code',
                           ),
         ),
     );

     $expected = array(
        'file1' => array(
                         'name' => 'file',
                         'type' => 'file_type',
                         'tmp_name' => 'file_tmp_name',
                         'size' => 'file_size',
                         'error' => 'file_err_code'
                         ),
        'form' => array(
          'file1' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
          'file2' => array(
             'name' => 'file',
             'type' => 'file_type',
             'tmp_name' => 'file_tmp_name',
             'size' => 'file_size',
             'error' => 'file_err_code'
           ),
         ),
     );

     $result = $this->parser->parse($files);
     $this->assertEqual($result, $expected);
  }
}


