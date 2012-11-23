<?php

require_once("CPF/php/Interfaces.php");
require_once("CPF/php/IO/FileUtils.class.php");

class MarkupParser implements IPlugInManifest
{
  public static function getPlugInDependencies()
  {
    $dependencies = array(
      "types" => array( "js" ),
      "js" => array(
          "markup_parser.js"
      )
    );

    return $dependencies;
  }
    
  public static function insertMarkup($markupFilename, $name, $showUpdateTime = false)
  {
    $content = htmlspecialchars(FileUtils::loadFile($markupFilename), ENT_IGNORE);

    // Setup divs for HTML output (written on page load)
    // and hidden span for markup source (passed to JS parser)
    echo "<div id=markup_".$name."_html></div>" . PHP_EOL;

    // Insert original markup as hidden text    
    echo "<span id=markup_".$name."_source style='display: none;'>".$content."</span>" . PHP_EOL;
   
    if (!$showUpdateTime)
      return;
 
    // Write file 'last updated' indication below content
    $mtime = filemtime($markupFilename);
    if ($mtime != FALSE)
      echo "<span id=content_".$name."._mtime style='font-style: italic'>"
           ."Last updated: ".date('F d, Y', $mtime)."<br><br></span>" . PHP_EOL;
  }
}

?>
