<?php
/**
 * @name Interfaces for CPF
 * @version 0.5 [July 14, 2012]
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

interface IPageNode
{
  function __construct(PageManager $pageMgr);
  function draw($cssClass = "");
}

interface IPageDBAdapter extends IPageNode
{
  function configure(DBmysql $database, $tableName);
}

?>
