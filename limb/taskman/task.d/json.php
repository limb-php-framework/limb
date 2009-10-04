<?php

/**
 * @desc Encodes tasks as json array
 */
function task_json()
{
  $json = array();
  foreach(taskman_gettasks() as $task)
  {
    $json[$task->getName()] = $task->getProps();
  }
  echo json_encode($json);
}

