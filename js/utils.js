/**
 * @name Utility functions for CPF
 * @version 0.5 [July 14, 2012]
 * @author Scott Coppen
 * @fileoverview
 * Basic utility functions used for CPF (Content Presentation Framework)
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

function numbersOnly(field, event)
{
  var k;
  var c;

  if (window.event)
    k = window.event.keyCode;
  else if (event)
    k = event.which;
  else
    return true;

  // control keys
  if ((k==null) || (k==0) || (k==8) || (k==9) || (k==13) || (k==27))
    return true;

  // numbers
  if ((("0123456789").indexOf(String.fromCharCode(k)) > -1))
    return true;

  return false;
}

function dateOnly(field, event)
{
  var k;
  var c;

  if (window.event)
    k = window.event.keyCode;
  else if (event)
    k = event.which;
  else
    return true;

  // control keys
  if ((k==null) || (k==0) || (k==8) || (k==9) || (k==13) || (k==27))
    return true;

  // date characters
  if ((("0123456789/").indexOf(String.fromCharCode(k)) > -1))
    return true;

  return false;
}

function legalCharactersOnly(field, event)
{
  var k;
  var c;

  if (window.event)
    k = window.event.keyCode;
  else if (event)
    k = event.which;
  else
    return true;

  // control keys
  if ((k==null) || (k==0) || (k==8) || (k==9) || (k==13) || (k==27))
    return true;

  // illegal characters
  if ((("!@#$%^&*()+=<>/?';\"`~[]{}\\").indexOf(String.fromCharCode(k)) == -1))
    return true;

  return false;
}

function urlencode(str)
{
  return encodeURIComponent(str).replace(/\%20/g, '+').replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/\~/g, '%7E');
}

function nl2br(str)
{
  return (str + '').replace(/([^>]?)\n/g, '$1'+'<br>'+'\n');
}
