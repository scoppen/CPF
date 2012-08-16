<?php
/**
 * @name HTMLFormatter class for CPF
 * @version 0.5 [July 14, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Base class for handling HTML formatting of generic content (page,
 *   body, table, etc.) for CPF (Content Presentation Framework)
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

class HTMLFormatter
{
  private $mCSSPrefix;
  private $mDivsArray;
  private $mStylePath;
  private $mScriptPath;
  private $mPageLevel;
  private $mTableLevel;

  protected function __construct($stylePath, $pageLevel = 2)
  {
    $this->mCSSPrefix = array("default");
    $this->mDivsArray = array(0);
    $this->mStylePath = $stylePath;
    $this->mScriptPath = "/CPF/js";
    $this->mPageLevel = $pageLevel;
    $this->mTableLevel = 0;
  }

  private function __clone() { }
  
  public function getTableLevel()
  {
    return $this->mTableLevel;
  }

  public function getNumTables()
  {
    return ceil($this->mTableLevel / 3);
  }

  public function getStylePath()
  {
    return $this->mStylePath;
  }

  public function setStylePath($path)
  {
    $this->mStylePath = $path;
  }

  public function getScriptPath()
  {
    return $this->mScriptPath;
  }

  public function setScriptPath($path)
  {
    $this->mScriptPath = $path;
  }

  protected function defineHeaderScripts($uri)
  {
    echo "<script type='text/javascript'"
        ." src='".$this->mScriptPath."/page_scripts.js'></script>" . PHP_EOL;
    echo "<script type='text/javascript'"
        ." src='".$this->mScriptPath."/utils.js'></script>" . PHP_EOL;
  }

  public function beginPage($title = "Untitled", $uri = "")
  {
    if ($this->mPageLevel != 0)
      return;

    echo "<!DOCTYPE HTML>"
        ."<html xmlns='http://www.w3.org/1999/xhtml' "
        ."xml:lang='en' lang='en'>" . PHP_EOL
        ."<head><meta http-equiv='Content-Type' content='text/html'>" . PHP_EOL
        ."<title>".$title."</title>" . PHP_EOL;

    echo "<link rel=stylesheet type=text/css"
        ." href='".$this->mStylePath."/stylesheet.css'>" . PHP_EOL;
    
    $this->defineHeaderScripts($uri);
    $this->mPageLevel = 1;
  }

  public function endPage()
  {
    if ($this->mPageLevel == 0)
      return;

    $this->endBody();
    $this->mPageLevel = 0;

    echo "</html>" . PHP_EOL;
  }

  public function beginBody()
  {
    if ($this->mPageLevel == 0)
      $this->beginPage("Untitled");
    else if ($this->mPageLevel == 2)
      return;

    echo "</head>" . PHP_EOL;
    $this->mPageLevel = 2;
    echo "<body>" . PHP_EOL;
  }
  
  public function endBody()
  {
    if ($this->mPageLevel < 2)
      return;
   
    echo "</body>" . PHP_EOL;
    $this->mPageLevel = 1;
  }

  protected function beginAnchor($href, $title = "")
  {
    if ($this->mPageLevel != 2)
      $this->beginBody();

    echo "<a href='".$href."'";
    if ($title != "")
      echo " title='".$title."'";

    echo ">";
  }

  protected function endAnchor()
  {
    if ($this->mTableLevel == 0)
      return;
   
    echo "</a>"; 
  }

  protected function beginDivisions($wdth = 0, $style= "", $cssClasses = "")
  {
    if ($this->mPageLevel != 2)
      $this->beginBody();

    $divs = explode(',', $cssClasses);
    foreach ($divs as $index => &$div)
    {
      echo "<div class='".$div."'";
      if (($index == 0) && ($style != ""))
        echo " style='".$style."'";

      echo ">";
    }

    array_push($this->mDivsArray, count($divs));
  }

  protected function endDivisions()
  {
    if ($this->mTableLevel == 0)
      return;
    
    $count = array_pop($this->mDivsArray);
    for ($i = 0; $i < $count; $i++)
      echo "</div>";
  }

  protected function beginTable($wdth = 0, $style= "", $cssClass = "")
  {
    if ($this->mPageLevel != 2)
      $this->beginBody();

    $this->mTableLevel++;
    if ($cssClass != "")
      $this->mCSSPrefix[$this->mTableLevel] = $cssClass;
    else
      $this->mCSSPrefix[$this->mTableLevel] =
        $this->mCSSPrefix[$this->mTableLevel - 1];

    echo "<table";
    if ($wdth != 0)  echo " width=$wdth";
    echo " class=".$this->mCSSPrefix[$this->mTableLevel];
    if ($style != "")
      echo " style='".$style."'";
    
    echo ">" . PHP_EOL;
  }
  
  protected function endTable()
  {
    if ($this->mTableLevel == 0)
      return;

    echo "</table>".PHP_EOL;
    $this->mTableLevel--;
  }

  protected function beginTableRow($hght = 0, $style = "", $cssClass = "")
  {
    if ($this->mPageLevel != 2)
      $this->beginBody();

    $this->mTableLevel++;
    if ($cssClass != "")
      $this->mCSSPrefix[$this->mTableLevel] = $cssClass;
    else
      $this->mCSSPrefix[$this->mTableLevel] =
        $this->mCSSPrefix[$this->mTableLevel - 1];
   
    echo "<tr";
    if ($hght != 0)  echo " height=$hght";
    echo " class=".$this->mCSSPrefix[$this->mTableLevel];
    if ($style != "")
      echo " style='".$style."'";
    
    echo ">" . PHP_EOL;
  }
  
  protected function endTableRow()
  {
    if ($this->mTableLevel == 0)
      return;

    echo "</tr>".PHP_EOL;
    $this->mTableLevel--;
  }

  protected function beginTableRowData($wdth = 0, $style = "", $cssClass = "")
  {
    if ($this->mPageLevel != 2)
      $this->beginBody();

    $this->mTableLevel++;
    if ($cssClass != "")
      $this->mCSSPrefix[$this->mTableLevel] = $cssClass;
    else
      $this->mCSSPrefix[$this->mTableLevel] =
        $this->mCSSPrefix[$this->mTableLevel - 1];
    
    echo "<td";
    if ($wdth != 0)  echo " width=$wdth";
    echo " class=".$this->mCSSPrefix[$this->mTableLevel];
    if ($style != "")
      echo " style='".$style."'";
    
    echo ">" . PHP_EOL;
  }

  protected function endTableRowData()
  {
    if ($this->mTableLevel == 0)
      return;
    
    echo "</td>".PHP_EOL;
    $this->mTableLevel--;
  }

  public static function tagArrayValues($array, $tag)
  {
    if (!is_array($array))
      return;

    foreach($array as &$value)
      echo "<".$tag.">".$value."</".$tag.">";
  }

  public static function tagArrayKeyedValues($array, $tag, $attr)
  {
    if (!is_array($array))
      return;

    foreach($array as $key => &$value)
      echo "<".$tag." ".$attr."='".$key."'>".$value."</".$tag.">";
  }
}

?>
