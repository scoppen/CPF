<?php
/**
 * @name Interfaces for CPF
 * @version 0.6 [September 4, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Interfaces used in CPF (Content Presentation Framework)
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

require_once("CPF/php/PageManager.class.php");

interface IDatabase
{
  function isConnected();
  function getDatabase();
  function getTableAutoIncrement($tableName);
  function getTableComment($tableName);
  function getTableIsNullables($tableName);
  function getTableDataTypes($tableName);
  function getTableColumnTypes($tableName);
  function getTableColumnComments($tableName);
  function getTableNumericPrecisions($tableName);
  function getTableCharacterMaxLengths($tableName);
  function getTableCharacterOctetLengths($tableName);
  function getTableEnumeratorColumn($tableName);
  function getTableEnumerationArray($tableName);
  function select($query);
}

interface IPageNode
{
  function __construct(PageManager $pageMgr);
  function draw($cssClass = "");
}

interface IComponent extends IPageNode
{
  function getComponentType();  
  function preDraw();
  function postDraw();
}

interface IPageDBAdapter extends IPageNode
{
  function configure(IDatabase $database, $tableName);
}

interface IPlugInManifest
{
  static function getPlugInDependencies();
}

?>
