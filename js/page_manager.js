/**
 * @name PageManger for CPF
 * @version 0.5 [July 14, 2012]
 * @author Scott Coppen
 * @fileoverview
 * AJAX handler for CPF (Content Presentation Framework) PageManager 
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

var xmlhttp;
var onPageContentLoaded;

function PageManager(page) {
    this.page = page;

    xmlhttp = getXmlHttpObject();

    this.getWidth = function() {
        if (self.innerWidth) // all except IE
            return self.innerWidth;

        if (document.documentElement &&
            document.documentElement.clientWidth) // IE 6 strict
            return document.documentElement.clientWidth;

        if (document.body) // other IE's
            return document.body.clientWidth;
    }
    
    this.getHeight = function() {
        if (self.innerHeight) // all except IE
            return self.innerHeight;

        if (document.documentElement &&
            document.documentElement.clientHeight) // IE 6 strict
            return document.documentElement.clientHeight;

        if (document.body) // other IE's
            return document.body.clientHeight;
    }

    this.showPageContent = function() {
        if ((xmlhttp.readyState == 4) || (xmlhttp.readyState=="complete")) {
            if (xmlhttp.status == 200) {
                document.getElementById('content_body').innerHTML =
                    xmlhttp.responseText; 
                onPageContentLoaded();
            }
        }
    }

    this.loadPageContent = function(callback) {
        onPageContentLoaded = callback;
        var url = this.page;
        url = url + '&window_width=' + this.getWidth();
        url = url + '&window_height=' + this.getHeight();
        xmlhttp.onreadystatechange = this.showPageContent;
        xmlhttp.open('GET', url, true);
        xmlhttp.send(null);
    }
}
