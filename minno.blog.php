<?php

/*
 * Function: blog
 * Manages a blog on a site.
 * Usage: <minno:blog params="blogname" />
 */

function minno_blog( $params )
{   
   $html = '';
   $blog = empty($params['blog']) ? 'blog' : $params['blog'];
   $limit = empty($params['limit']) ? 10 : $params['limit'];
   
   if ( auth() ) 
   {
      if ( $_POST['blog'] )
      {  
         $fn = date('Y/m/d/His-') . preg_replace('/\s+/','-',preg_replace('/[^a-z0-9 ]/i','',$_POST['title'])) . '.html';
         //create blog post//
         $content = "<article>\n<header><h1>\n<a href=\"". $blog . '/' .  $fn .'">' . $_POST['title'];
         $content .= "</a></h1>\n<p>Published: <time pubdate=\"pubdate\">". date('Y-m-d') ."</time></p></header>\n";
         $content .= $_POST['blog'] . "\n</article>";
         file_out( $blog . DS .  $fn, $content );
      }
      
      $html .= form('<input type="text" size="80" name="title" placeholder="Title" id="title" /><br /><textarea cols="80" rows="20" id="blog" name="blog"></textarea><br />');
   }
   
   $html .= minno_inc(array('id' => $blog . '/*/*/*/*.html','rsort' => 1,'limit' => $limit));
   
   return $html;
  
}