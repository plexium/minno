<?php

/*
 * Function: protect
 * Provides a low level protection to pages
 * Usage: <minno:protect params="username,password" />
 */

function minno_protect($params)
{	
	if ( $_SERVER['PHP_AUTH_USER'] != $params['username'] && $_SERVER['PHP_AUTH_PW'] != $params['password'] )
	{
		header('WWW-Authenticate: Basic realm="Login"');
		header('HTTP/1.0 401 Unauthorized');		
		exit();
	}
}
