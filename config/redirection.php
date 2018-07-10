<?php
use Dto\Screen;
/**
 * Simple Rules for redirection.
 * prev => Previous screen
 * current => current screen
 * next => next screen
 * 
 */
$config['redirection'] = array(
		'login' => array(
				new Screen('user/login', 'empty', DEFAULT_REDIRECT_AFTER_LOGIN),				
				new Screen('user/login', 'users/activate', DEFAULT_REDIRECT_AFTER_LOGIN),
				new Screen('user/login', 'my/profile', DEFAULT_REDIRECT_AFTER_LOGIN)
		),
		
		'edit' => array(
				new Screen('user/edit', 'my/booking', 'prev'),
				new Screen('user/edit', 'my/simple', 'prev'),				
				new Screen('user/activate ', 'any', 'users/edit'),
		)
		
);