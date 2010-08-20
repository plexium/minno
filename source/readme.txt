MINNO
-----

Minno was an experiment to see just how small I could make a web-editable CMS system. 

However it is actually kind of useful. There's an empty spot somewhere between a full blown
cms and a static driven site. This is where i can see minno or a deriviatve of minno working well.

Static sites don't adhear to the DRY principle. Editing pages and scaling a site is difficult. 
CMS systems have bloat and unnecessary processing for small sites. Custom designs and layouts are
harder to achieve. 

Minno is a hybrid between static and dynamic. It adhears to DRY but still makes editing easier than static.
It is also very small bloat and allows for custom designs.

The key would be to extend minno to give a small set of useful tools to web developers not affraid of html
but don't want to manage a static site.

Requirements of Minno
 - be small and simple
 - allow complete control over site
 - allow complete control over api
 - be secure
 
One current downfall of minno is security. The issue being allowing access to execute full php given a correct user login. 
This is both a feature and huge security risk.
Some solutions:
 - limit php to specific function calls and disallow editing php pages
 - strip out all php in edit screens
 - relocate data folder


sample minno tags
<minno:i params="header.php" />




