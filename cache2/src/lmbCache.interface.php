<?php

interface lmbCache
{
  const OPERATION_ADD = 'ADD';
  const OPERATION_SET = 'SET';
  const OPERATION_GET = 'GET';
  const OPERATION_DELETE = 'DELETE';
  const OPERATION_LOCK = 'LOCK';
  const OPERATION_UNLOCK = 'UNLOCK';
  const OPERATION_INCREMENT = 'INCREMENT';
  const OPERATION_DECREMENT = 'DECREMENT';
  
  function set($key, $value);
  function get($key);
  function delete($key);
}