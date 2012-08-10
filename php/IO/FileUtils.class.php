<?php
/**
 * @name FileUtils class for CPF
 * @version 0.5 [July 14, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Class of static methods for common file IO
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

class FileUtils
{
  public static function dirsAsArray($dirPath)
  {
    $dirs = array();
    $files = array_diff(scandir($dirPath), array('.', '..'));
    foreach ($files as $file)
    {
      if (is_dir($dirPath."/".$file))
        $dirs[] = $file;
    }

    return $dirs;
  }

  public static function pathAsArray($dirPath)
  {
    return explode('/', $dirPath);
  }

  public static function pagesAsArray($dirPath)
  {
    $pages = array();
    $files = array_diff(scandir($dirPath), array('.', '..'));
    foreach ($files as $file)
    {
      if (is_dir($dirPath."/".$file) || (strlen($file) < 3) ||
          (substr_compare($file, '.js', -3) === 0))
        continue;

      if ((substr_compare($file, '.php', -4) === 0) &&
          in_array($file.'.js', $files))
        $pages[] = $file;
    }

    return $pages;
  } 

  public static function loadFile($filename)
  {
    $fd = fopen($filename, 'r');
    $content = fread($fd, filesize($filename));
    fclose($fd);

    return $content;
  }
}

?>
