CPF
===

Content Presentation Framework for webpages (pronounced 'see-puff')

Control presentation of content using PHP, Javascript, and MySQL database backend
with programmatic structures.  Basic classes to handle HTML formatting, page layouts,
dynamic JS/CSS loading, and MySQL database queries already provided - derive from 
these to create a rich and dynamic presentation!  Or, extend from the framework
interfaces and build your own presentation widgets.

All content is dynamically loaded once initial exchange of data between client - data
exchange includes client window size such that PHP/JS code can determine ideal layout.

Basic setup:
 * add an alias for CPF to the full path:

    Alias /CPF/ "/usr/share/PHP/CPF/"

    <Directory "/usr/share/PHP/CPF">
      Options IncludesNoExec
      AllowOverride None
      Order allow, deny
      Allow from all
    </Directory>

 * make sure every webpage has a corresponding JS file:
    ie. file 'index.php' would require 'index.php.js'

Enjoy! 
