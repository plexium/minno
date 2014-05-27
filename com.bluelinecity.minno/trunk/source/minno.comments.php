<?php

/*
 * Function: comments
 * Manages user submitted comments to a particular piece of content. Requires a moderation function.
 * Usage: <minno:comments />
 */

function minno_comments( $params )
{
   global $_8;

   $source = $_8[count($_8)-1];

   $comments = glob( $source . '.comment.*.html' );
   
   //post comment if found//
   if ( $_POST['comment'] )
   {
      $pending_comments = glob( dirname($source) . DS . '.' . basename($source) . '.comment.*.html' );      
      $fn = preg_replace('/^'. preg_quote($GLOBALS['store']) . '/','',dirname($source)) . DS . '.' . basename($source) . '.comment.' . (count($pending_comments) + count($comments)) . '.html';

      //create blog post//
      $content = "<article>\n<header><h1>\n<a href=\"". $blog . '/' .  $fn .'">' . $_POST['title'];
      $content .= "</a></h1>\n<p>Published: <time pubdate=\"pubdate\">". date('Y-m-d') ."</time></p></header>\n";
      $content .= $_POST['comment'] . "\n</article>";
      file_out( $fn, $content );
      $html = '<span class="pending-comment">Your comment is pending approval...<br /></span>';
   }
   else
   {
      $html = form('<textarea cols="30" rows="2" id="comment" name="comment"></textarea><br />');
   }
   
   foreach ( $comments as $file )
   {
      $html .= '<article>' . file_in($file) . '</article>';
   }
      
   return '<section class="comments"><h3>'. count($comments) .' Comments</h3>' . $html . '</section>';  
}