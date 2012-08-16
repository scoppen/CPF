<?php
/**
 * @name LayoutController class for CPF
 * @version 0.6 [August 16, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Controller class for basic webpage layouts (1, 2, and 3 vertical 'panes'
 *   of content) for CPF (Content Presentation Framework)
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

require_once("CPF/php/HTMLFormatter.class.php");

abstract class LayoutController extends HTMLFormatter
{
  private $mPageMinWidth;
  private $mLeftSideBarWidth;
  private $mRightSideBarWidth;
  private $mPadding;
  private $mLayoutLevel;
  private $mScripts;

  protected function __construct($stylePath, $pageMinWidth,
      $leftSideBarWidth, $rightSideBarWidth, $padding = 8)
  {
    parent::__construct($stylePath, 0);
    $this->mPageMinWidth = $pageMinWidth;
    $this->mLeftSideBarWidth = $leftSideBarWidth;
    $this->mRightSideBarWidth = $rightSideBarWidth;
    $this->mPadding = $padding;
    $this->mLayoutLevel = 0;
    $this->mScripts = array();
  }

  private function __clone() { }

  abstract public function getPageWidth();
  abstract public function getPageHeight();

  public function getMaxTextInputSize()
  {
    return (int)(($this->mPageMinWidth - $this->mLeftSideBarWidth - $this->mRightSideBarWidth) / 10);
  }

  public function getLeftSideBarWidth()
  {
    if (($this->getPageWidth() - $this->mPageMinWidth - $this->getRightSideBarWidth()) < $this->mLeftSideBarWidth)
      return 0;

    return $this->mLeftSideBarWidth;
  }

  public function getRightSideBarWidth()
  {
    if (($this->getPageWidth() - $this->mPageMinWidth - $this->mLeftSideBarWidth) < $this->mRightSideBarWidth)
      return 0;

    return $this->mRightSideBarWidth;
  }

  public function getCurrentWidth()
  {
    switch($this->mLayoutLevel)
    {
    case 0:
    case 1:	// Header
    case 5:	// Footer
      return $this->getPageWidth();

    case 2:	// Center panel (content) 
      return $this->getPageWidth() - 
          $this->getLeftSideBarWidth() - $this->getRightSideBarWidth();

    case 3:	// Left sidebar 
      return $this->getLeftSideBarWidth();

    case 4:	// Right sidebar
      return $this->getRightSideBarWidth();
    }

    return 0;
  }

  protected function vSkip($skip)
  {
    $this->beginTableRow($skip);
    $this->endTableRow();
  }

  protected function hSkip($skip)
  {
    $this->beginTableRowData($skip);
    $this->endTableRowData();
  }

  protected function defineHeaderScripts($uri)
  {
    parent::defineHeaderScripts($uri);

    if (!empty($_SERVER) && !empty($_SERVER['PHP_SELF']))
    {
      echo "<script type='text/javascript' "
          ."src='".$_SERVER['PHP_SELF'].".js'></script>" . PHP_EOL;
    }
  }

  public function beginPage($title, $uri, $screenWidth, $screenHeight)
  {
    parent::beginPage($title, $uri);
    
    $pageWidth = $this->mPageMinWidth + $this->mLeftSideBarWidth + $this->mRightSideBarWidth; 
    $pageMinWidth = ($screenWidth < $pageWidth) ? $screenWidth : $pageWidth;
//    $pageMinWidth = 720;

    echo "<style type='text/css' title='main_layout'>" . PHP_EOL;
    echo "body { "
        ."margin: 0; padding: 0; border: 0; width: 100%; "
        ."font-family: Tahoma, Arial, Veranda; "
        ."font-size: 80%; "
        ."background: #fff; "
        ."min-width: " . $pageMinWidth . "px; "
        ."max-width: " . 2 * $pageMinWidth . "px; "
        ."}" . PHP_EOL;
    echo "#content_body { "
//        ."width: " . $pageMinWidth . "px; "
//        ."height: 100%; "
        ."margin: 0 auto; padding-bottom: 10px; "
        ."}" . PHP_EOL;
    echo "#header { "
        ."clear: both; float: left; width: 100%; "
        ."}" . PHP_EOL;
    echo "#menu { "
        ."width: " . $pageMinWidth . "px; "
        ."height: 40px; "
        ."}" . PHP_EOL;
    echo "#footer { "
        ."clear: both; float: left; width: 100%; "
        ."border-top: 1px solid #111; "
        ."}" . PHP_EOL;
    echo "</style>" . PHP_EOL;
    
    $this->beginBody();
  }

  public function beginPopup($title, $uri, $screenWidth, $screenHeight)
  {
    parent::beginPage($title, $uri);
  
    $pageMinWidth = ($screenWidth < 640) ? $screenWidth - 20 : 620;
    $this->mPageMinWidth = $pageMinWidth - 50; 
    $this->mLeftSideBarWidth = 0;
    $this->mRightSideBarWidth = 0;

    echo "<style type='text/css' title='main_layout'>" . PHP_EOL;
    echo "body { "
        ."margin: 0; padding: 0; border: 0; width: 100%; "
        ."font-family: Tahoma, Arial, Veranda; "
        ."font-size: 80%; "
        ."background: #fff; "
        ."min-width: " . $pageMinWidth . "px; "
        ."max-width: " . $pageMinWidth . "px; "
        ."}" . PHP_EOL;
    echo "#content_body { "
        ."width: " . $pageMinWidth . "px; "
//        ."height: 100%; "
        ."margin: 0 auto; padding-bottom: 10px; "
        ."}" . PHP_EOL;
    echo "#header { "
        ."clear: both; float: left; width: 100%; "
        ."border-bottom: 1px solid #000; "
        ."}" . PHP_EOL;
    echo "#menu { "
        ."width: " . $pageMinWidth . "px; "
        ."height: 40px; "
        ."}" . PHP_EOL;

    $this->layoutOneColumn();

    echo "#footer { "
        ."clear: both; float: left; width: 100%; "
        ."border-top: 1px solid #000; "
        ."}" . PHP_EOL;
    echo "</style>" . PHP_EOL;
    
    $this->beginBody();
  }

  public function endPopup()
  {
    parent::endPage();
  }

  private function layoutOneColumn()
  {
    $pageLeftMargin = -$this->mRightSideBarWidth;
    $centerColumnLeftMargin = $this->mLeftSideBarWidth + $this->mPadding;
    $centerColumnRightMargin = $this->mRightSideBarWidth + $this->mPadding;
    $leftColumnLeftOffset = $this->mLeftSideBarWidth + $this->mRightSideBarWidth;
    $leftColumnLeftPadding = $this->mPadding;
    $leftColumnContentWidth = $this->mLeftSideBarWidth - 2 * $this->mPadding;
    $rightColumnRightSpacing = 3 * $this->mPadding;
    $rightColumnContentWidth = $this->mRightSideBarWidth - 2 * $this->mPadding;

    echo ".colmask { "
        ."position: relative; clear: both; float: left; "
        ."width: 100%; overflow: hidden; "
        ."}" . PHP_EOL;
    echo ".threecolumn { "
        ."background: #ff9; "
        ."}" . PHP_EOL;
    echo ".threecolumn .hidden { "
        ."visibility: hidden; "
        ."}" . PHP_EOL;
    echo ".threecolumn .colcenter { "
        ."float: left; position: relative; width: 200%; right: 100%; "
        ."margin-left: " . $pageLeftMargin . "px; "
        ."background: #fef; "
        ."}" . PHP_EOL;
    echo ".threecolumn .colleft { "
        ."float: left; position: relative; width: 100%; margin-left: -50%; "
        ."left: " . $leftColumnLeftOffset . "px; "
        ."background: #FFD8B7; "
        ."}" . PHP_EOL;
    echo ".threecolumn .col1wrap { "
        ."float: left; position: relative; width: 50%; "
        ."right: " . $this->getLeftSideBarWidth() . "px; "
        ."}" . PHP_EOL;
    echo ".threecolumn .col1 { "
        ."position: relative; overflow: hidden; left: 200%; "
        ."margin-left: " . $centerColumnLeftMargin . "px; "
        ."margin-right: " . $centerColumnRightMargin . "px; "
        ."}" . PHP_EOL;
    echo ".threecolumn .col2 { "
        ."float: left; float: right; position: relative; "
        ."width: " . $leftColumnContentWidth . "px; "
        ."right: " . $leftColumnLeftPadding . "px; "
        ."}" . PHP_EOL;
    echo ".threecolumn .col3 { "
        ."float: left; float: right; position: relative; left: 50%; "
        ."width: " . $rightColumnContentWidth . "px; "
        ."margin-right: " . $rightColumnRightSpacing . "px; "
        ."}" . PHP_EOL;
  }

  public function endPage()
  {
    if (($this->mLayoutLevel == 1) || ($this->mLayoutLevel == 5))
      echo "</div>" . PHP_EOL;
    else if (($this->mLayoutLevel < 5) && ($this->mLayoutLevel != 0))
      echo "</div></div></div></div>" . PHP_EOL;

    parent::endPage();
  }

  public function beginBody()
  {
    parent::beginBody();
  }

  public function endBody()
  {
    parent::endBody();
  }

  public function beginPageHeader()
  {
    if ($this->mLayoutLevel == 1)
      return;
    else if ($this->mLayoutLevel > 1)
    {
      echo "ERROR: beginPageHeader() called out of order!" . PHP_EOL;
      return;
    }
    
    echo "<div id=header>" . PHP_EOL;
    $this->mLayoutLevel = 1;
  }

  public function beginPageContent()
  {
    if ($this->mLayoutLevel == 2)
      return;
    else if ($this->mLayoutLevel > 2)
    {
      echo "ERROR: beginPageContent() called out of order!" . PHP_EOL;
      return; 
    }
    else
      echo "</div>" . PHP_EOL;

    echo "<div class='colmask threecolumn'>"
        ."<div class=colcenter><div class=colleft>"
        ."<div class=col1wrap><div class=col1>" . PHP_EOL;
    $this->mLayoutLevel = 2;
  }
  
  public function beginPageLeftSideBar()
  {
    if ($this->mLayoutLevel == 3)
      return;
    else if ($this->mLayoutLevel != 2)
    {
      echo "ERROR: beginPageLeftSideBar() called out of order!" . PHP_EOL;
      return; 
    }
    else
      echo "</div></div>" . PHP_EOL;

    echo "<div class=col2>" . PHP_EOL;
    $this->mLayoutLevel = 3;
  }

  public function beginPageRightSideBar()
  {
    if ($this->mLayoutLevel == 4)
      return;
    else if ($this->mLayoutLevel != 3)
    {
      echo "ERROR: beginPageLeftSideBar() called out of order!" . PHP_EOL;
      return; 
    }
    else
      echo "</div>" . PHP_EOL;

    echo "<div class=col3>" . PHP_EOL;
    $this->mLayoutLevel = 4;
  }
  
  public function beginPageFooter()
  {
    if ($this->mLayoutLevel == 1)
      echo "</div>" . PHP_EOL;
    else if ($this->mLayoutLevel < 5)
      echo "</div></div></div></div>" . PHP_EOL;
    else if ($this->mLayoutLevel == 5)
      return;
    else
    {
      echo "ERROR: beginPageContent() called out of order!" . PHP_EOL;
      return; 
    }

    echo "<div id=footer>" . PHP_EOL;
    $this->mLayoutLevel = 5;
  }
  
  protected function beginTable($wdth = 0, $style = "", $cssClass = "")
  {
    $level = parent::getTableLevel();
    switch ($level % 3)
    {
      case 1:
        parent::beginTableRow();
        // Fall through...

      case 2:
        parent::beginTableRowData();
        // Fall through...

      case 0:
        parent::beginTable($wdth, $style, $cssClass);
        break;
    }
  }

  protected function endTable()
  {
    $level = parent::getTableLevel();
    switch ($level % 3)
    {
    case 0:
      if ($level == 0)
        return;

      parent::endTableRowData();
      // Fall through...

    case 2:
      parent::endTableRow();
      // Fall through...

    case 1:
      parent::endTable();
      break;
    }
  }

  protected function beginTableRow($hght = 0, $style = "", $cssClass = "")
  {
    $level = parent::getTableLevel();
    switch ($level % 3)
    {
      case 0:
        if (parent::getTableLevel() == 0)
        {
          parent::beginTable(0, $style, $cssClass);
          parent::beginTableRow($hght, $style, $cssClass);
          break;
        }
        else
          parent::endTableRowData();
	// Fall through...
      
      case 2:
        parent::endTableRow();
        // Fall through...

      case 1:
        parent::beginTableRow($hght, $style, $cssClass);
        break;
    }
  }

  protected function endTableRow()
  {
    $level = parent::getTableLevel();
    switch ($level % 3)
    {
      case 1:
        break;

      case 0:
        if (parent::getTableLevel() == 0)
          break;

        parent::endTableRowData();
	// Fall through...

      case 2:
        parent::endTableRow();
        break;
    }
  }

  protected function beginTableRowData($wdth = 0, $style, $cssClass = "")
  {
    $level = parent::getTableLevel();
    switch ($level % 3)
    {
      case 0:
        if (parent::getTableLevel() == 0)
        {
          parent::beginTable(0, $style, $cssClass);
          parent::beginTableRow(0, $style, $cssClass);
          parent::beginTableRowData($wdth, $style, $cssClass);
        }
        else
        {
          parent::endTableRowData();
          parent::beginTableRowData($wdth, $style, $cssClass);
        }
	break;

      case 1:
        parent::beginTableRow(0, $style, $cssClass);
        // Fall through...
      
      case 2:
        parent::beginTableRowData($wdth, $style, $cssClass);
        break;
    }
  }

  protected function endTableRowData()
  {
    $level = parent::getTableLevel();
    switch ($level % 3)
    {
    case 0:
      if ($level == 0)
        return;

      parent::endTableRowData();
      break;

    case 1:
    case 2:
      break;
    }
  }

}

?>
