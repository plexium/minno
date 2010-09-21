<?php

/*
 * Function: protect
 * Provides a low level protection to pages
 * Usage: <minno:protect params="username,password" />
 */

function protect()
{
	$args = func_get_args();
	
	if ( $_SERVER['PHP_AUTH_USER'] != $args[0] && $_SERVER['PHP_AUTH_PW'] != $args[1] )
	{
		header('WWW-Authenticate: Basic realm="Login"');
		header('HTTP/1.0 401 Unauthorized');		
		exit();
	}
}
