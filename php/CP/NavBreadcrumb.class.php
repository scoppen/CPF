<?php

require_once("CPF/php/UI/ListSet.class.php");
require_once("CPF/php/IO/FileUtils.class.php");


class NavBreadcrumb extends ListSet implements IComponent
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

    parent::draw('breadcrumb');
  }

  protected function drawListItem($label, $href, $extraCSSclass)
  {
    parent::drawListItem(ucwords($label), $this->mPageMgr->getTopPath()."/".$href, $extraCSSclass);
    if ($extraCSSclass != "active")
      echo "<span class='divider'>/</span>";
  }
}

?>
