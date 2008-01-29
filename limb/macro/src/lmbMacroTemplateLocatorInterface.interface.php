<?php

interface lmbMacroTemplateLocatorInterface {
  function __construct($config);
  function locateSourceTemplate($file_name);
  function locateCompiledTemplate($file_name);
}