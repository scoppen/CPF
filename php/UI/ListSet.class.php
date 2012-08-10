<?php
/**
 * @name ListSet class for CPF
 * @version 0.5 [July 23, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Formats an array (key,value) pairs into HTML unordered list
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

require_once("CPF/php/Interfaces.php");
require_once("CPF/php/PageManager.class.php");
require_once("CPF/php/HTMLFormatter.class.php");


class ListSet extends HTMLFormatter implements IPageNode
{
  private $mListItems;
  private $mActiveListItemKey;

  public function __construct(PageManager $pageMgr)
  {
    parent::__construct($pageMgr->getStylePath());
    $this->mListItems = array();
    $this->mActiveListItemKey = '';
  }

  private function __clone() { }

  public function setListItems($items)
  {
    $this->mListItems = $items;
  }

  public function setActiveListItemKey($key)
  {
    $this->mActiveListItemKey = $key;
  }

  public function draw($extraCSSclass = "")
  {
    echo "<ul class='".$extraCSSclass."'>" . PHP_EOL;

    foreach ($this->mListItems as $key => &$value)
    {
      $active = ($key == $this->mActiveListItemKey);
      $this->drawListItem($key, $value, ($active != FALSE) ? "active" : "");
    } 
 
    echo "</ul>" . PHP_EOL; 
  }

  protected function drawListItem($label, $href, $extraCSSclass = "")
  {
    echo "<li";
    if ($extraCSSclass != "")
      echo " class='".$extraCSSclass."'>";
    else
      echo ">";
    
    if (strcmp($extraCSSclass, "nav-header") == 0)
      echo $label;
    else
      echo "<a href=".$href.">".$label."</a></li>" . PHP_EOL;
  }
}

?>
