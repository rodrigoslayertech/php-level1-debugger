<?php
function Debug(...$vars){
    if(\Debug::$trace === null){
        \Debug::$trace = debug_backtrace();
    }

    new \Debug(...$vars);

    if(\Debug::$trace !== false){
        \Debug::$trace = null;
    }
}
