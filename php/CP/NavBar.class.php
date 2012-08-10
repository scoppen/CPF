<?php
/**
 * @name NavBar class for CPF
 * @version 0.5 [July 23, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Uses list of sub-directories to populate navigation bar entries
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


class NavBar extends ListSet
{
  private $mPageMgr;
  private $mSubDirs;

  public function __construct(PageManager $pageMgr)
  {
    parent::__construct($pageMgr);
    $this->mPageMgr = $pageMgr;

    $dirs = FileUtils::dirsAsArray($pageMgr->getRootPath());

    $this->mSubDirs = array();
    foreach ($dirs as $dir)
    {
      if ((strcmp($dir, 'include') == 0) ||
          (substr($dir, 0, 1) === '_'))
        continue;

      $this->mSubDirs[urldecode($dir)] = $dir;
    }
  }

  private function __clone() { }
  
  protected function getSubDirs()
  {
    return $this->mSubDirs;
  }

  protected function drawPreListContent() { }
  
  protected function drawPostListContent() { }

  public function draw($extraCSSclass = "")
  {
    $basePath = $this->mPageMgr->getBasePath();
    $topPath = $this->mPageMgr->getTopPath();
    $subPath = $this->mPageMgr->getSubPath();
    
    $this->setListItems($this->mSubDirs);

    // Determine active list item key
    $active = (strcmp($basePath, $topPath) == 0);
    if ($active != FALSE)
      $this->setActiveListItemKey('Home');
    else if (in_array($subPath, $this->mSubDirs))
      $this->setActiveListItemKey(array_search($subPath, $this->mSubDirs));

    echo "<div class='navbar ".$extraCSSclass."'>" . PHP_EOL;
    echo "<div class='navbar-inner'><div class='container'>" . PHP_EOL;
   
    $this->drawPreListContent();
    parent::draw('nav');
    $this->drawPostListContent();

    echo "</div></div></div>" . PHP_EOL; 
  }

  protected function drawListItem($label, $href, $extraCSSclass)
  {
    //if ($href !== ucfirst($href))
    //  return;

    parent::drawListItem(ucwords($label), $this->mPageMgr->getTopPath()."/".$href, $extraCSSclass);
  }
}

?>
