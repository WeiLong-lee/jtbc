<?php
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
namespace jtbc {
  class page
  {
    public static $counter = 0;
    public static $db = null;
    public static $init = false;
    public static $para = array();
    private static $title = array();

    public static function db()
    {
      $db = null;
      if (!is_null(self::$db)) $db = self::$db;
      else
      {
        $db = new db();
        $db -> dbHost = DB_HOST;
        $db -> dbUsername = DB_USERNAME;
        $db -> dbPassword = DB_PASSWORD;
        $db -> dbDatabase = DB_DATABASE;
        $db -> init();
        if ($db -> errStatus != 0) $db = null;
        else self::$db = $db;
      }
      return $db;
    }

    public static function formatResult($argStatus, $argHTML)
    {
      $status = $argStatus;
      $html = $argHTML;
      $html = str_replace(']]>', ']]]]><![CDATA[>', $html);
      $tmpstr = '<?xml version="1.0" encoding="utf-8"?><result status="' . base::getNum($status, 0) . '"><![CDATA[' . $html . ']]></result>';
      return $tmpstr;
    }

    public static function formatMsgResult($argStatus, $argMessage, $argPara = '')
    {
      $status = $argStatus;
      $message = $argMessage;
      $para = $argPara;
      $tmpstr = '<?xml version="1.0" encoding="utf-8"?><result status="' . base::getNum($status, 0) . '" message="' . base::htmlEncode($message) . '" para="' . base::htmlEncode($para) . '"></result>';
      return $tmpstr;
    }

    public static function getPara($argName)
    {
      if (self::$init == false)
      {
        self::$init = true;
        self::init();
      }
      return self::$para[$argName];
    }

    public static function getResult()
    {
      $tmpstr = '';
      $type = request::getHTTPPara('type', 'get');
      $action = request::getHTTPPara('action', 'get');
      if (base::isEmpty($type)) $type = 'default';
      $class = get_called_class();
      $module = 'module' . ucfirst($type);
      if ($type == 'action') $module = 'moduleAction' . ucfirst($action);
      if (method_exists($class, 'start')) call_user_func(array($class, 'start'));
      if (method_exists($class, $module)) $tmpstr = call_user_func(array($class, $module));
      return $tmpstr;
    }

    public static function getPagePara($argName)
    {
      $name = $argName;
      $para = @self::$para[$name];
      if (base::isEmpty($para)) $para = tpl::take('global.public.' . $name, 'lng');
      return $para;
    }

    public static function getPageTitle()
    {
      $tmpstr = '';
      $title = self::$title;
      if (!empty($title))
      {
        foreach ($title as $key => $val)
        {
          $tmpstr = $val . SEPARATOR . $tmpstr;
        }
      }
      $tmpstr = $tmpstr . tpl::take('global.index.title', 'lng');
      return $tmpstr;
    }

    public static function setPagePara($argName, $argValue)
    {
      $name = $argName;
      $value = $argValue;
      self::$para[$name] = $value;
      return $value;
    }

    public static function setPageTitle($argTitle)
    {
      $title = $argTitle;
      if (!base::isEmpty($title)) array_push(self::$title, $title);
      return self::getPageTitle();
    }

    public static function init()
    {
      self::$para['http'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
      self::$para['http_host'] = $_SERVER['HTTP_HOST'];
      self::$para['route'] = smart::getRoute();
      self::$para['genre'] = smart::getActualGenre(self::$para['route']);
      self::$para['assetspath'] = ASSETSPATH;
      self::$para['global.assetspath'] = smart::getActualRoute(ASSETSPATH);
      self::$para['folder'] = base::getLRStr($_SERVER['SCRIPT_NAME'], '/', 'leftr') . '/';
      self::$para['filename'] = base::getLRStr($_SERVER['SCRIPT_NAME'], '/', 'right');
      self::$para['uri'] = $_SERVER['SCRIPT_NAME'];
      self::$para['urs'] = $_SERVER['QUERY_STRING'];
      self::$para['url'] = self::$para['uri'];
      self::$para['urlpre'] = self::$para['http'] . self::$para['http_host'];
      if (!base::isEmpty(self::$para['urs'])) self::$para['url'] .= '?' . self::$para['urs'];
    }
  }
}
//******************************//
// JTBC Powered by jtbc.cn      //
//******************************//
?>