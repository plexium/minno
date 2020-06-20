<?php

/*
 * Function: seo stuff
 */

function seo_title( $tag = 'h2' )
{	
	$f = file_in($GLOBALS['store'] . validate_path($GLOBALS['id']));
	preg_match('/\<'.$tag.'[^>]*\>(.+)\<\/'.$tag.'\>/',$f, $matches );
	return $matches[1];
}

 
function meta_description( )
{	
	$args = func_get_args();
	$def = implode(',', $args);
	$f = file_in($GLOBALS['store'] . validate_path($GLOBALS['id']));
	$found = preg_match('/\<p[^>]* class="[^"]*seo\-description[^"]*"[^>]*\>((\w|\W)+?)\<\/p\>/i',$f, $matches );
	return '<meta name="description" content="' . ( $found ? trim(preg_replace('/\r|\n/',' ',htmlspecialchars(substr(strip_tags($matches[1]),0,255)))) : $def ) . '" />';
}


function meta_keywords()
{
	$def = func_get_args();
	$f = file_in($GLOBALS['store'] . validate_path($GLOBALS['id']));
	$found = preg_match('/\<p[^>]* class="[^"]*seo\-keywords[^"]*"[^>]*\>((\w|\W)+?)\<\/p\>/i',$f, $matches );
	return '<meta name="keywords" content="' . implode(',',$def) . ( $found && !empty($def) ? ',' : '' ) . trim(preg_replace('/\r|\n/',' ',htmlspecialchars(substr(strip_tags($matches[1]),0,255)))) . '" />';
}


function sitemap()
{
	$exclude = func_get_args();
	$xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
	$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		
	$list = _rglob('*.html',0, $GLOBALS['store'] );
	
	foreach ( $list as $file )
	{
		$url = str_replace( $GLOBALS['store'], '', $file);
		if ( in_array( $url, $exclude ) ) continue;
		$xml .= "\n\n<url>\n";
		$xml .= "   <loc>http://{$_SERVER['SERVER_NAME']}/{$GLOBALS['base']}{$url}</loc>\n";
		$xml .= "   <lastmod>". date('Y-m-d',filemtime($file)) ."</lastmod>\n";
		$xml .= "   <changefreq>weekly</changefreq>\n";
		$xml .= "   <priority>0.5</priority>\n";
		$xml .= "</url>\n";
	}
		
	$xml .= '</urlset>';
	
	$fp = fopen('sitemap.xml','w');
	fwrite($fp,$xml);
	fclose($fp);
	
	$submit = urlencode('http://' . $_SERVER['SERVER_NAME'] .'/' . $GLOBALS['base'] . 'sitemap.xml');
	return 'Sitemap created at <a href="sitemap.xml">'. urldecode($submit) .'</a><br /><ul>'
		. '<li><a target="sitemap" href="http://www.google.com/webmasters/tools/ping?sitemap='.$submit.'">Submit to Google</a></li>'
		. '<li><a target="sitemap" href="http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid=SitemapWriter&url='.$submit.'">Submit to Yahooo!</a></li>'
		. '<li><a target="sitemap" href="http://submissions.ask.com/ping?sitemap='.$submit.'">Submit to Ask.com</a></li>'
		. '<li><a target="sitemap" href="http://www.bing.com/webmaster/ping.aspx?siteMap='.$submit.'">Submit to Bing</a></li>'
		. '</ul>';
}


function _rglob($pattern='*', $flags = 0, $path='')
{
    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
    $files=glob($path.$pattern, $flags);
    foreach ($paths as $path) { $files=array_merge($files,_rglob($pattern, $flags, $path)); }
    return $files;
}