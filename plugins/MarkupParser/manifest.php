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
    
  public static function insertMarkup($markupFilename)
  {
    $content = htmlspecialchars(FileUtils::loadFile($markupFilename), ENT_IGNORE);

    // Setup divs for HTML output (written on page load)
    // and hidden span for markup source (passed to JS parser)
    echo "<div id=content_html></div>" . PHP_EOL;

    // Write file 'last updated' indication below content
    $mtime = filemtime($markupFilename);
    if ($mtime != FALSE)
      echo "<span id=content_mtime style='font-style: italic'>"
           ."Last updated: ".date('F d, Y', $mtime)."</span>" . PHP_EOL;

    // Insert original markup as hidden text    
    echo "<span id=content_markup class=hidden>".$content."</span>" . PHP_EOL;
  }
}

?>
