<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUncaughtExceptionHandlingFilter.class.php 5772 2007-04-25 08:02:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');
lmb_require('limb/core/src/lmbErrorGuard.class.php');

class lmbUncaughtExceptionHandlingFilter implements lmbInterceptingFilter
{
  const CONTEXT_RADIUS = 3;
  protected $toolkit;

  function run($filter_chain)
  {
    $this->toolkit = lmbToolkit :: instance();

    lmbErrorGuard :: registerFatalErrorHandler($this, 'handleFatalError');
    lmbErrorGuard :: registerExceptionHandler($this, 'handleException');

    $filter_chain->next();
  }

  function handleFatalError($error)
  {
    $this->toolkit->getLog()->error($error['message']);
    $this->toolkit->getResponse()->reset();

    $message = $error['message'];
    $trace = '';
    $file = $error['file'];
    $line = $error['line'];
    $context = htmlspecialchars($this->_getFileContext($file, $line));
    $request = htmlspecialchars($this->toolkit->getRequest()->dump());

    for($i=0; $i < ob_get_level(); $i++)
      ob_end_clean();

    $session = htmlspecialchars($this->toolkit->getSession()->dump());
    echo $this->_renderTemplate($message, $trace, $file, $line, $context, $request, $session);
    exit(1);
  }

  function handleException($e)
  {
    if(function_exists('debugBreak'))
      debugBreak();

    $this->toolkit->getLog()->exception($e);
    $this->toolkit->getResponse()->reset();

    $error = htmlspecialchars($e->getMessage());
    $trace = htmlspecialchars($e->getTraceAsString());
    list($file, $line) = $this->_extractExceptionFileAndLine($e);
    $context = htmlspecialchars($this->_getFileContext($file, $line));
    $request = htmlspecialchars($this->toolkit->getRequest()->dump());
    $session = htmlspecialchars($this->toolkit->getSession()->dump());

    for($i=0; $i < ob_get_level(); $i++)
      ob_end_clean();

    echo $this->_renderTemplate($error, $trace, $file, $line, $context, $request, $session);
    exit(1);
  }

  protected function _renderTemplate($error, $trace, $file, $line, $context, $request, $session)
  {
    $formatted_error = nl2br($error);

    $body = <<<EOD
<html>
<head>
  <title>{$error}</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    body { background-color: #fff; color: #333; }

    body, p, ol, ul, td {
      font-family: verdana, arial, helvetica, sans-serif;
      font-size:   13px;
      line-height: 25px;
    }

    pre {
      background-color: #eee;
      padding: 10px;
      font-size: 11px;
      line-height: 18px;
    }

    a { color: #000; }
    a:visited { color: #666; }
    a:hover { color: #fff; background-color:#000; }
  </style>

  <script>
  function TextDump() {
    w = window.open('', "Error text dump", "scrollbars=yes,resizable=yes,status=yes,width=1000px,height=800px,top=100px,left=100px");
    w.document.write('<html><body>');
    w.document.write('<h1>' + document.getElementById('Title').innerHTML + '</h1>');
    w.document.write(document.getElementById('Context').innerHTML);
    w.document.write(document.getElementById('Trace').innerHTML);
    w.document.write(document.getElementById('Request').innerHTML);
    w.document.write(document.getElementById('Session').innerHTML);
    w.document.write('</body></html>');
    w.document.close();
  }
  </script>
</head>
<body>
<h2 id='Title'>{$formatted_error}</h2>

<a href="#" onclick="document.getElementById('Trace').style.display='none';document.getElementById('Context').style.display='block'; return false;">Context</a> |

<a href="#" onclick="document.getElementById('Trace').style.display='block';document.getElementById('Context').style.display='none'; return false;">Call stack</a> |

<a href="#" onclick="TextDump(); return false;">Raw dump</a>

<div id="Context" style="display: block;">
<h3>Error in '{$file}' around line {$line}:</h3>
<pre>{$context}</pre>
</div>

<div id="Trace" style="display: none;">
<h3>Call stack:</h3>
<pre>{$trace}</pre>
</div>

<div id="Request">
<h2>Request</h2>
<pre>{$request}</pre>
</div>

<div id="Session">
<h2>Session</h2>
<pre>{$session}</pre>
</div>

</body>
</html>
EOD;
    return $body;
  }

  protected function _extractExceptionFileAndLine($e)
  {
    if($e instanceof WactException)
    {
      $params = $e->getParams();
      if(isset($params['file']))
        return array($params['file'], $params['line']);
    }
    return array($e->getFile(), $e->getLine());
  }

  protected function _getFileContext($file, $line_number)
  {
    $context = array();
    $i = 0;
    foreach(file($file) as $line)
    {
      $i++;
      if($i >= $line_number - self :: CONTEXT_RADIUS && $i <= $line_number + self :: CONTEXT_RADIUS)
        $context[] = $i . "\t" . $line;

      if($i > $line_number + self :: CONTEXT_RADIUS)
        break;
    }

    return "\n" . implode("", $context);
  }
}

?>