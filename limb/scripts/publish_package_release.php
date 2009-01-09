<?php
set_time_limit(0);

$PEAR = 'http://pear.limb-project.com/back.php';

if($argc < 4)
{
  echo "Usage: publish_package_release <dir> <user> <password>";
  exit(1);
}

$pkg = $argv[1];
$user = $argv[2];
$password = $argv[3];
$file = create_archive($pkg);
login($user, $password);
upload($file);
unlink($file);

echo "File '$file' uploaded\n";

function create_archive($dir)
{
  $old = getcwd();
  chdir($dir);

  if(!is_file("./VERSION"))
    fatal("VERSION file is not present\n");

  list($name, $version) = explode('-', trim(file_get_contents("./VERSION")));

  system("php package.php", $res);

  if($res != 0)
    fatal("Package XML creation error\n");

  system("pear package", $res);

  if($res != 0)
    fatal("'pear package' failed\n");

  unlink("package.xml");
  chdir($old);

  return "$dir/$name-$version.tgz";
}

function login($user, $password)
{
  global $PEAR;

  $login_form = array("user" => $user,
                      "password" => $password,
                      "login" => "Login");
  $curl = new CURL();
  $page = $curl->post($PEAR, $login_form);

  if(preg_match('~Invalid\s+Login~', $page))
    fatal("Could not login to admin page\n");
}

function upload($file)
{
  global $PEAR;

  $upload_form = array("MAX_FILE_SIZE" => "2097152",
                       "submitted" => '1',
                       "release" => "@$file",
                       "Submit" => 'Submit',
                       "f" => "0");
  $curl = new CURL();

  $page = $curl->post($PEAR, $upload_form);

  if(!preg_match('~Release\s+successfully\s+saved~', $page))
     fatal("Could not upload release '$file'\n");
}

class CURL
{
  protected $handle;
  protected $opts = array();

  function __construct()
  {
    $this->verbose(false);
  }

  protected function _ensureCurl()
  {
    if(!is_resource($this->handle))
     $this->handle = curl_init();
  }

  protected function _resetCurl()
  {
    if(is_resource($this->handle))
      curl_close($this->handle);
    $this->opts = array();
  }

  protected function _browserInit()
  {
    @mkdir(dirname(__FILE__) . '/tmp');
    $this->setOpt(CURLOPT_HEADER, 1);
    $this->setOpt(CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322;)");
    $this->setOpt(CURLOPT_FOLLOWLOCATION, 1);
    $this->setOpt(CURLOPT_COOKIEJAR, dirname(__FILE__) . '/tmp/cookie.txt');
    $this->setOpt(CURLOPT_COOKIEFILE, dirname(__FILE__) . '/tmp/cookie.txt');
    $this->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
    $this->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
    if($proxy = getenv('http_proxy'))
      $this->setOpt(CURLOPT_PROXY, $proxy);
  }

  function exec()
  {
    $this->_ensureCurl();

    foreach($this->opts as $opt => $value)
      curl_setopt($this->handle, $opt, $value);

    $res = curl_exec($this->handle);
    if (curl_errno($this->handle) == 0)
    {
      $this->_resetCurl();
      return $res;
    }
    else
    {
      $error = curl_error($this->handle);
      $this->_resetCurl();
      fatal($error . "\n");
    }
  }

  function get($url)
  {
    $this->_browserInit();
    $this->setOpt(CURLOPT_URL, $url);
    $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
    return $this->exec();
  }

  function post($url, $vars)
  {
    $this->_browserInit();
    $this->setOpt(CURLOPT_URL, $url);
    $this->setOpt(CURLOPT_POST, 1);
    $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
    $this->setOpt(CURLOPT_POSTFIELDS, $vars);
    return $this->exec();
  }

  function verbose($flag = true)
  {
    $this->setOpt(CURLOPT_VERBOSE, $flag ? 1 : 0);
  }

  function setOpt($opt, $value)
  {
    $this->opts[$opt] = $value;
  }
}

function fatal($msg)
{
  echo $msg;
  exit(1);
}

