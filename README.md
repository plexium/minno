# minno

Minno is an incredibly small (<4kB) PHP CMS. The best way to describe it is a CMS between statically 
maintained web sites and a full-blown database driven CMS.

## Is Minno for you?

Are you not afraid of working directly in HTML and CSS and believe in the DRY principle?

Do you like to edit via a web interface instead of locally and through FTP?

Do you need to create very small static-like web sites with minimal content updates and a single admin login?

__Then Minno might be right for you!__

## Features

* Doesn't require a database
* Gives complete control over html
* Can be extended via simple php functions
* Allows adhearance to the DRY principle when creating web pages
* Incredibly small

## Documentation

### Installation

1. Download the latest version of Minno and unzip the files.

2. You should have the following three files.

3. .htaccess - Keeps the urls pretty by forcing all requests through Minno

index.php - Configuration file and CMS front-end.

minno.php - The minno CMS.

Open index.php and edit the $user and $pass variables with what you would like the login to be. 
Other configuration variables that can be specified here include:
$index - default web page. Defaults to 'index.html'.
$store - path to the data folder where all the web pages are saved. Defaults to 'data/'.
$base - base url path in case the web site is in a subfolder. Defaults to ''.
$functions - path to extra functions folder. Defaults to ''.
Create a data folder to hold the web site's data files. and make it world writable. Minno defaults to 'data/'.
Now go to the site in your web browser. Minno should detect no "core" file and will create one for you. The core is essentally the HTML wrapper called for every page request on Minno. This is where your site design gets placed.
You will be automatically logged in after the script installs. So you can begin editing your web site.

### Site Creation

To login to Minno a single password field is given. You must enter your username and password separated by a semi-colon.

After logging in you can edit any non-existant page simply by attempting to go to it in your browser. To update a current page append '?edit' to it in your address bar ( ie, about.html?edit ).

#### Minno Tags

Minno tags are special namespace tags that get replaced with the results of a user-defined function when displayed by Minno. Only functions not prefixed with an underscore can be used.

The two built-in Minno Tags are <minno:inc/> and <minno:login/>. You can pass parameters via the params attribute and separating multiple arguments with a comma.

<minno:inc params="page.html"/>
This tag includes any page named in the params. If left blank then the inc() function will assume you want to include the page initially requested by the user. You can also use wildcards in the params attribute and include multiple files.
<minno:login/>
This tag displays a login/logout form.

### Extra Functions

Minno is extended using simple user-defined php functions. Minno auto-loads any file named "function.*.php". Any functions defined can be called using the Minno namespace tag. What the function returns is used to replace the minno tag in the page content.
