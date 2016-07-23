<?php
function Debug(...$vars){
	$trace = debug_backtrace();
	\Debug::$trace = $trace[0];
	new \Debug(...$vars);
	\Debug::$trace = null;
}
