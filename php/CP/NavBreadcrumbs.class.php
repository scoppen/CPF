<?php
/**
 * @name NavBreadcrumbs class for CPF
 * @version 0.6 [September 4, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Uses directory tree as list to populate navigation links
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


class NavBreadcrumbs extends ListSet implements IComponent
{
  private $mPageMgr;
  private $mSubDirs;

  public function __construct(PageManager $pageMgr)
  {
    parent::__construct($pageMgr);
    $this->mPageMgr = $pageMgr;

    $dirs = FileUtils::pathAsArray($pageMgr->getSubPath());

    $this->mSubDirs = array();
    foreach ($dirs as $dir)
    {
      if ($dir != '')
        $this->mSubDirs[urldecode($dir)] = $dir;
    }
  }

  private function __clone() { }

  protected function getSubDirs()
  {
    return $this->mSubDirs;
  }

  public function getComponentType() { return "ListSet"; }

  public function preDraw() { }

  public function postDraw() { }

  public function draw($extraCSSclass = "")
  {
    $basePath = $this->mPageMgr->getBasePath();
    $topPath = $this->mPageMgr->getTopPath();
    $subPath = $this->mPageMgr->getSubPath();

    $this->setListItems(array_merge(array('Home' => ''), $this->mSubDirs));

    // Determine active list item key
    $active = (strcmp($basePath, $topPath) == 0);
    if ($active != FALSE)
      $this->setActiveListItemKey('Home');
    else if (in_array($subPath, $this->mSubDirs))
      $this->setActiveListItemKey(array_search($subPath, $this->mSubDirs));

    $this->preDraw();
    parent::draw('breadcrumb');
    $this->postDraw();
  }

  protected function drawListItem($label, $href, $extraCSSclass)
  {
    parent::drawListItem(ucwords($label), $this->mPageMgr->getTopPath()."/".$href, $extraCSSclass);
    if ($extraCSSclass != "active")
      echo "<span class='divider'>/</span>";
  }
}

?>
