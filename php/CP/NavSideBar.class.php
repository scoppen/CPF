<?php
/**
 * @name NavSideBar class for CPF
 * @version 0.5 [July 23, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Uses list of pages to populate navigation sidebar entries
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

require_once("CPF/php/UI/ListSet.class.php");
require_once("CPF/php/IO/FileUtils.class.php");


class NavSideBar extends ListSet
{
  private $mPageMgr;
  private $mSubPages;

  public function __construct(PageManager $pageMgr)
  {
    parent::__construct($pageMgr);
    $this->mPageMgr = $pageMgr;

    $pages = FileUtils::pagesAsArray($pageMgr->getWorkPath());

    $this->mSubPages = array();
    foreach ($pages as $page)
    {
      $pos = strripos($page, '.php');
      $label = urldecode(substr($page, 0, $pos));
      if ((strcmp($label, 'index') != 0) &&
          (substr($label, 0, 1) != '_'))
        $this->mSubPages[$label] = $page;
    }
  }

  private function __clone() { }
  
  protected function drawPreListContent() { }
  
  protected function drawPostListContent() { }

  public function draw($extraCSSclass = "")
  {
    $basePath = $this->mPageMgr->getBasePath();
    $topPath = $this->mPageMgr->getTopPath();
    $subPath = $this->mPageMgr->getSubPath();

    $this->setListItems($this->mSubPages);

    // TODO: Determine active list item key
    /* $active = (strcmp($basePath, $topPath) == 0);
    if ($active != FALSE)
      $this->setActiveListItemKey('Home');
    else if (in_array($subPath, $this->mSubDirs))
      $this->setActiveListItemKey(array_search($subPath, $this->mSubDirs)); */
    
    echo "<div class='well' style='padding: 8px 0;'>";

    $this->drawPreListContent();
    parent::draw('nav '.$extraCSSclass);
    $this->drawPostListContent();
    
    echo "</div>";
  }

  protected function drawListItem($label, $href, $extraCSSclass)
  {
    $path = $this->mPageMgr->getTopPath()."/".$this->mPageMgr->getSubPath();
    parent::drawListItem(ucwords($label), $path."/".$href, $extraCSSclass);
  }
}

?>
