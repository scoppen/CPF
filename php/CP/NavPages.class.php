<?php
/**
 * @name NavPages class for CPF
 * @version 0.6 [August 16, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Uses list of pages to populate navigation entries
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


class NavPages extends ListSet implements IComponent
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
  
  public function getComponentType() { return "ListSet"; }
  
  public function preDraw() { }
  
  public function postDraw() { }

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
    
    $this->preDraw();
    parent::draw('nav '.$extraCSSclass);
    $this->postDraw();
  }

  protected function drawListItem($label, $href, $extraCSSclass)
  {
    $path = $this->mPageMgr->getTopPath()."/".$this->mPageMgr->getSubPath();
    parent::drawListItem(ucwords($label), $path."/".$href, $extraCSSclass);
  }
}

?>
