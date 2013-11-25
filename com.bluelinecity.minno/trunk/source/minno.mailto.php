<?php

/*
 * Function: mailto
 * Creates an obsfucated mailto link
 * Usage: <minno:mailto address="myname" domain="mydomain.com" />
 */

function minno_mailto( $params )
{
   return '<script language="javascript">var email = "'.$params['address']
   .'" + "@" + "'.$params['domain'].'";document.write("<a href=\'mailto:"+ email +"\'>"+email+"</a>");</script><noscript>'
   .$params['address'].' at '.$params['domain'].'</noscript>';
}