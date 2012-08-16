/**
 * @name Page scripts for CPF
 * @version 0.6 [August 16, 2012]
 * @author Scott W Coppen
 * @fileoverview
 * Generic page scripts for CPF (Content Presentation Framework)
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

function addScript(scriptSrc)
{
  var head = document.getElementsByTagName('head')[0] || document.documentElement;
  var script = document.createElement('script');
  if (scriptSrc.scriptCharset) {
    script.charset = scriptSrc.scriptCharset;
  }

  script.type = 'text/javascript';
  script.src = scriptSrc.url;

  head.appendChild(script);
}

function addScript(scriptSrc, callback)
{
  var head = document.getElementsByTagName('head')[0] || document.documentElement;
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = scriptSrc;
  
  var done = false;
  script.onload = script.onreadystatechange = function() {
    if (!done && (!this.readyState ||
          this.readyState === "loaded" || this.readyState === "complete")) {
      done = true;
      callback();
      script.onload = script.onreadystatechange = null;
      if (head && script.parentNode) {
        head.removeChild(script);
      }
    }
  };

  head.insertBefore(script, head.firstChild);
}

function addStyleSheet(filename, cssType)
{
  var head = document.getElementsByTagName('head')[0] || document.documentElement;
  var link = document.createElement('link');
  link.href = filename;
  link.rel = 'stylesheet';
  link.type = cssType;
  link.media = 'all';

  head.appendChild(link);
}

function getStyleSheetByTitle(title)
{
  for(var i=0; i<document.styleSheets.length; i++) {
    var sheet = document.styleSheets[i];
    if(sheet.title == title) {
      return sheet;
    }
  }
}

function appendStyleSheetRule(stylesheet, selector, rule)
{
  if (document.all)  // IE
    stylesheet.addRule(selector, rule);
  else
    stylesheet.insertRule('' + selector + ' { ' + rule + ' }', stylesheet.cssRules.length);
}

function submitForm(form)
{
  if (form.length==0)
    return;

  document.forms[form].submit();
}

function getXmlHttpObject()
{
  var xmlhttp = null;
  try { xmlhttp = new XMLHttpRequest(); } // all except IE
  catch (e) // IE
  {
    try { xmlhttp = new ActiveXObject("Msxml2.XMLHTTP"); }
    catch (e) { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }
  }

  return xmlhttp;
}

