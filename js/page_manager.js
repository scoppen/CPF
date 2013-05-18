/**
 * @name PageManger for CPF
 * @version 0.7 [May 18, 2013]
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
var onPostPageLayout;
var onPageContentLoaded;
var onPostPageLoaded;

function PageManager(page) {
    this.page = page;

    xmlhttp = getXmlHttpObject();

    this.setPageLayout = function(params) {
        var lsbw = params.left_sidebar_width;
        var rsbw = params.right_sidebar_width;

        // Remove sidebar(s) if screen size cannot accomodate (left goes first)
        if ((this.getWidth() - params.min_width - rsbw) < lsbw)
            lsbw = 0;
        
        if ((this.getWidth() - params.min_width - lsbw) < rsbw) 
            rsbw = 0;

        // Compute stylesheet parameters
        var plm = -rsbw;
        var cclm = lsbw + params.padding;
        var ccrm = rsbw + params.padding;
        var lclo = lsbw + rsbw;
        var lclp = params.padding;
        var lccw = lsbw - 2 * params.padding;
        var rcrs = 3 * params.padding;
        var rccw = rsbw - 2 * params.padding;

        // Insert stylesheet attributes for 3-column layout
        var ss = getStyleSheetByTitle('main_layout');
        appendStyleSheetRule(ss,'.colmask',
            'position: relative; clear: both; float: left; width: 100%; overflow: hidden');
        appendStyleSheetRule(ss,'.threecolumn .hidden',
            'visibility: hidden');
        appendStyleSheetRule(ss,'.threecolumn .colcenter',
            'float: left; position: relative; width: 200%; right: 100%; ' +
            'margin-left: ' + plm + 'px; background: inherit');
        appendStyleSheetRule(ss,'.threecolumn .colleft',
            'float: left; position: relative; width: 100%; margin-left: -50%; ' +
            'left: ' + lclo + 'px');
        appendStyleSheetRule(ss,'.threecolumn .col1wrap',
            'float: left; position: relative; width: 50%; ' +
            'right: ' + lsbw + 'px');
        appendStyleSheetRule(ss,'.threecolumn .col1',
            'position: relative; overflow: hidden; left: 200%; ' +
            'margin-left: ' + cclm + 'px; margin-right: ' + ccrm + 'px');
        appendStyleSheetRule(ss,'.threecolumn .col2',
            'float: left; float: right; position: relative; ' +
            'width: ' + lccw + 'px; right: ' + lclp + 'px');
        appendStyleSheetRule(ss,'.threecolumn .col3',
            'float: right; position: relative; left: 50%; ' +
            'width: ' + rccw + 'px; margin-right: ' + rcrs + 'px');
    }

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
                if (onPostPageLoaded)
                    onPostPageLoaded();
            }
        }
    }

    this.setOnPostPageLayout = function(callback) {
        var getType = {};
        if (callback && getType.toString.call(callback) == '[object Function]') {
            onPostPageLayout = callback;
        }
    }

    this.setOnPostPageLoaded = function(callback) {
        var getType = {};
        if (callback && getType.toString.call(callback) == '[object Function]') {
            onPostPageLoaded = callback;
        }
    }

    this.loadPageContent = function(callback, params) {
        onPageContentLoaded = callback;
        this.setPageLayout(params);

        var width = this.getWidth();
        var height = this.getHeight();

        if (onPostPageLayout)
            onPostPageLayout(width, height);

        var url = this.page;
        url = url + '&window_width=' + width;
        url = url + '&window_height=' + height;
        xmlhttp.onreadystatechange = this.showPageContent;
        xmlhttp.open('GET', url, true);
        xmlhttp.send(null);
    }
}
