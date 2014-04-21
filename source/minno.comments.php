<?php

/*
 * Function: comments
 * Manages user submitted comments to a particular piece of content.
 * Usage: <minno:blog params="blogname" />
 */

function minno_comments( $params )
{
   global $_8;

   $source = $_8[count($_8)-1];
   $comments = glob( $source . '.comment.*.html' );

   if ( auth() )
   {
      if ( $_POST['comment'] )
      {  
         $fn = dirname($source) . DS . '.' . basename($source) . '.comment.' . count($comments) . 'p.html';

         //create blog post//
         $content = "<article>\n<header><h1>\n<a href=\"". $blog . '/' .  $fn .'">' . $_POST['title'];
         $content .= "</a></h1>\n<p>Published: <time pubdate=\"pubdate\">". date('Y-m-d') ."</time></p></header>\n";
         $content .= $_POST['comment'] . "\n</article>";
         echo $fn;
         file_out( $fn, $content );
      }
      
      if ( $_POST['moderate'] )
      {
         echo $_POST['submit'];
         if ( $_POST['submit'] == 'Approve' )
         {
            rename( $_POST['moderate'], preg_replace('/p\.html$/','.html',$_POST['moderate']) );
         }
         else
         {            
            unlink($_POST['moderate']);
         }
         
         $comments = glob( $source . '.comment.*.html' );
      }
   }
   
   $html = '';
   foreach ( $comments as $file )
   {
      if ( preg_match('/p\.html$/',$file) )
      {
         if ( auth() )
         {
            $html .= '<article class="pending">' . file_in($file) . 
            form('<input type="hidden" value="'. $file .'" name="moderate" />'.
               '<input type="submit" name="submit" value="Delete" />','Approve') .
            '</article>';
         }
      }
      else
      {
         $html .= '<article>' . file_in($file) . '</article>';
      }
   }
   
   $html .= form('<textarea cols="30" rows="2" id="comment" name="comment"></textarea><br />');
   
   return '<section class="comments"><h3>'. count($comments) .' Comments</h3>' . $html . '</section>';  
}