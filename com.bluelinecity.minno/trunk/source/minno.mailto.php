<?php

/*
 * Function: mailto
 * Creates an obsfucated mailto link
 * Usage: <minno:mailto params="address,domain" />
 */

function minno_mailto( $address, $domain )
{
   return '<script language="javascript">var email = "'.$address.'" + "@" + "'.$domain.'";document.write("<a href=\'mailto:"+ email +"\'>"+email+"</a>");</script><noscript>'.$address.' at '.$domain.'</noscript>';
}