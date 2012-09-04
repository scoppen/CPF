<?php
/**
 * @name PageManager class for CPF
 * @version 0.6 [September 4, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Class for handling page management functions (basic setup, script
 *   loading, etc.) for CPF (Content Presentation Framework)
 */

/*
 * Copyright 2012 Scott W Coppen
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once("CPF/php/LayoutController.class.php");

class PageManager extends LayoutController
{
  private $mWorkPath;
  private $mRootPath;
  private $mBasePath;
  private $mTopPath;
  private $mSubPath;
  private $mRegistered;
  private $mPageWidth;
  private $mPageHeight;
  private $mPageURI;
  private $mPageScripts;
  private $mPostScript;
  private $mPlugInScripts;
  private $mPlugInStyles;
 
  public function __construct($level = 0, $configFile = 'config.php')
  {
    // Work path is the current working directory
    // eg. /srv/mycompany.com/www/Products/
    $this->mWorkPath = getcwd();

    // Root path is the location of the site configuration 'include' 
    // directory (controlled by the 'level' parameter)
    $array = explode('/', $this->mWorkPath);
    $this->mRootPath =
        implode('/',array_slice($array, 0, count($array) - $level));
    
    // Base path is the work path with the server root path removed
    // eg. /Products/ 
    $this->mBasePath = 
        substr($this->mWorkPath, strlen($_SERVER['DOCUMENT_ROOT']), 256);

    // Top path is the path to the top level include    
    $array = explode('/', $this->mBasePath);
    $this->mTopPath =
        implode('/',array_slice($array, 0, count($array) - $level));

    $this->mSubPath =
        substr($this->mBasePath, strlen($this->mTopPath) + 1, 256);

    // Check if the proper parameters are included in the query
    $this->mRegistered = 
        isset($_GET['window_width']) && isset($_GET['window_height']);
    if ($this->mRegistered)
    {
      $this->mPageWidth = $_GET['window_width'];
      $this->mPageHeight = $_GET['window_height'];
    }

    // Add include path for this site    
    ini_set('include_path',
        ini_get('include_path').':'.$this->mRootPath.'/include');
    require_once $configFile;

    parent::__construct('/CPF/css/'.$gSiteConfig['css_style'],
        $gLayoutConfig['min_width'], $gLayoutConfig['left_sidebar_width'],
        $gLayoutConfig['right_sidebar_width']);
    
    // Use local scripts path if it exists
    if (is_dir($this->mRootPath.'/include/scripts'))
      $this->setScriptPath($this->mTopPath.'/include/scripts');

    $this->mPageURI = "";
    $this->mPageScripts = array();
    $this->mPostScript = "";
    $this->mPlugInScripts = array();
    $this->mPlugInStyles = array();
  }

  private function __clone() { }

  public function getWorkPath()
  {
    return $this->mWorkPath;
  }

  public function getRootPath()
  {
    return $this->mRootPath;
  }

  public function getBasePath()
  {
    return $this->mBasePath;
  }

  public function getTopPath()
  {
    return $this->mTopPath; 
  }

  public function getSubPath()
  {
    return $this->mSubPath; 
  }

  public function isRegistered()
  {
    return $this->mRegistered;
  }

  public function getPageWidth()
  {
    return $this->mPageWidth;
  }
  
  public function getPageHeight()
  {
    return $this->mPageHeight;
  }

  public function getPageURI()
  {
    return $this->mPageURI;
  }

  public function addPageScript($scriptFile)
  {
    $this->mPageScripts[] = $scriptFile;
  }

  public function addPlugIn($plugIn)
  {
    include_once("CPF/plugins/".$plugIn."/manifest.php");
    $dependencies = $plugIn::getPlugInDependencies();

    if (!array_key_exists('types', $dependencies))
      return;

    if (in_array('js', $dependencies['types']) &&
        array_key_exists('js', $dependencies))
    {
      foreach ($dependencies['js'] as &$value)
      {
        $jsfile = $plugIn."/".$value;
        $this->mPlugInScripts[] = $jsfile;
      }
    }

    if (in_array('css', $dependencies['types']) &&
        array_key_exists('css', $dependencies))
    {
      foreach ($dependencies['css'] as &$value)
      {
        $cssfile = $plugIn."/".$value;
        $this->mPlugInStyles[] = $cssfile;
      }
    }
  }

  protected function defineHeaderScripts($uri)
  {
    parent::defineHeaderScripts($uri);
    
    foreach ($this->mPlugInScripts as $value)
    {
      echo "<script type='text/javascript' "
          ."src='/CPF/plugins/".$value."'></script>" . PHP_EOL;
    }

    foreach ($this->mPageScripts as $value)
    {
      echo "<script type='text/javascript' "
          ."src='".$this->getScriptPath()."/".$value."'></script>" . PHP_EOL;
    }
    
    echo "<script type='text/javascript' language='javascript'>" . PHP_EOL
        ."  var pageManager;" . PHP_EOL
        ."  addScript('".$this->getScriptPath()."/page_manager.js'," . PHP_EOL
        ."    function() {" . PHP_EOL
        ."       pageManager = new PageManager('".$uri."');" . PHP_EOL;

    if ($this->mPostScript)
    {
        echo  "       pageManager.setOnPostPageLoaded(function() { ".$this->mPostScript." });" . PHP_EOL;
    }

    echo "    });" . PHP_EOL
        ."</script>" . PHP_EOL; 

    global $gLayoutConfig;

    echo "<script type='text/javascript' language='javascript'>" . PHP_EOL
        ."  window.onload = function(){ " . PHP_EOL
        ."    var interval = setInterval(function() { " . PHP_EOL
        ."      if (typeof pageManager != 'undefined') { " . PHP_EOL
        ."        clearInterval(interval);" . PHP_EOL
        ."        loadPage(".json_encode($gLayoutConfig).");" . PHP_EOL
        ."      } " . PHP_EOL
        ."    }, 50);" . PHP_EOL
        ."  };" . PHP_EOL
        ."</script>" . PHP_EOL;

    foreach ($this->mPlugInStyles as $value)
    {
      echo "<link rel='stylesheet' type='text/css' "
          ."href='/CPF/plugins/".$value."'></link>" . PHP_EOL;
    }
  }

  protected function setPostPageContentLoadedScript($script)
  {
    $this->mPostScript = $script;
  }

  protected function displayHeader()
  {

  }

  protected function displayPreContent()
  {

  }

  protected function displayLeftSideBar()
  {

  }

  protected function displayRightSideBar()
  {

  }

  protected function displayFooter()
  {
    echo "<p align='right'>Data presented using "
        ."<a href='http://www.github.com/scoppen/CPF'>CPF</a>"
        ." v0.6&nbsp;&nbsp;";
  }

  public function beginPage($title, $uri = "", $screenWidth = 800, $screenHeight = 400)
  {
    if (empty($uri))
    {
      $uri = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://";
      $uri .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      if (strpos($uri, '?') === FALSE)
        $uri .= "?sid=" . $_SERVER['REQUEST_TIME'].mt_rand();
      else
        $uri .= "&sid=" . $_SERVER['REQUEST_TIME'].mt_rand();
    }

    $this->mPageURI = $uri;
      
    if ($this->isRegistered())
    {
      parent::beginPageHeader();
      $this->displayHeader();
      parent::beginPageContent();
      $this->displayPreContent();
    }
    else
    {
      parent::beginPage($title, $uri, $screenWidth, $screenHeight);
      echo "<div id='content_body' name='content_body'></div>" . PHP_EOL;
    }
  }

  public function beginPopup($title, $uri = "", $screenWidth = 800, $screenHeight = 400)
  {
    if (empty($uri))
    {
      $uri = (!empty($_SERVER['HTTPS'])) ? "https://" : "http://"
             .$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      if (strpos($uri, '?') === FALSE)
        $uri .= "?sid=" . $_SERVER['REQUEST_TIME'].mt_rand();
      else
        $uri .= "&sid=" . $_SERVER['REQUEST_TIME'].mt_rand();
    }

    parent::beginPopup($title, $uri, $screenWidth, $screenHeight);
    echo "<div id='content_body' name='content_body'></div>" . PHP_EOL;
  }

  public function endPage()
  {
    if ($this->isRegistered())
    {
      parent::beginPageLeftSideBar();
      $this->displayLeftSideBar();
      parent::beginPageRightSideBar();
      $this->displayRightSideBar();
      parent::beginPageFooter();
      $this->displayFooter();
    }
    else
      parent::endPage();
  }
  
  public function endPopup()
  {
    parent::endPopup();
  }
}

?>
