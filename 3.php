<?php
/*
 * --------------------------------------------------------------------------
 * Developed by Rodrigo Vieira (@Slowaways)
 * Copyright 2017
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */
class Debug
{
    // _
        // _Config
    public static $debug = true;
    public static $print = true;
    public static $exit = true;
    public static $cli = false;

    // Identifiers
    public static $ips;
    public static $call = 1; // int
    public static $title; // string
    public static $trace; // array
    public static $vars; // array
        public static $labels; // array

    // Delimiters
        // Call
        public static $from;
        public static $to;
        // Title
        public static $search;
        // Stack
        public static $stacks;

    // .Output
    public static $Output;

    public function __construct(...$vars){
        if(self::$debug === false)
            return;
        if( !empty(self::$ips) ){
            foreach(self::$ips as $ip){
                $founded = false;
                if($_SERVER['REMOTE_ADDR'] == $ip){
                    $founded = true;
                    break;
                }
            }

            if($founded === false)
                return;
        }


        // CLI
        if(@PHP_SAPI === 'cli'){
            self::$cli = true;
        }

        // Title
        $title = self::$title;
        // Vars
        if(empty($vars) and self::$vars){
            $vars = self::$vars;
        }

        // Count
        $call = self::$call;

        // To
        if(self::$to === null){
            $to = self::$call;
        }
        else{
            $to = self::$to;
        }
        // From
        if(self::$from and (self::$from <=> self::$to) !== -1){
            self::$from = null;
        }
        $from = self::$from;
        // Search
        if(self::$search === null){
            $search = self::$title;
        }
        else{
            $search = self::$search;
        }

        // Catch
        if( ( ($from and $call >= $from) or $call >= $to ) and $search == $title ){
            if(self::$trace !== false and self::$trace === null){
                $trace = debug_backtrace();
                self::$trace = $trace;
            }

            $this->generate($vars);

            // Print
            if(self::$print)
                print self::$Output;

            self::$trace = null;

            if(self::$exit){
                if(self::$from == null){
                    exit;
                }
                else{
                    if(self::$to == self::$call){
                        exit;
                    }
                }
            }
        }

        if(self::$to and self::$search){
            if($search == self::$title){
                self::$call++;
            }
        }
        else{
            self::$call++;
        }
    }

    public static function input(...$vars){
        self::$vars = $vars;
    }
    public static function reset(){
        self::$call = 1;
        self::$from = null;
        self::$to = null;
        self::$search = null;
        self::$title = null;
        self::$labels = null;
    }

    public static function dump($value){
        switch( gettype($value) ){
            case 'boolean':
                $type = 'boolean';
                $prefix = "<small>$type</small> ";
                $info = '';
                $color = '#75507b';

                if($value)
                    $var = 'true';
                else
                    $var = 'false'; break;
            case 'integer':
                $type = 'int';
                $prefix = "<small>$type</small> ";
                $info = '';
                $color = '#4e9a06';
                $var = $value; break;
            case 'double': // float
                $type = 'float';
                $prefix = "<small>$type</small> ";
                $info = '';
                $color = "#f57900";
                $var = $value; break;
            case 'string':
                $type = 'string';
                $prefix = "<small>$type</small> ";
                $info = '(length='.strlen($value).') ';
                $color = '#cc0000';
                $var = "'".$value."'"; break;
            case 'array':
                $type = 'array';
                $prefix = "<b>$type</b>";
                $info = ' (size='.count($value).") ";
                $color = '';
                $array = $value;
                $identity = "\t\t\t";

                $var = '';
                foreach($array as $key => $value){
                    if( is_string($key) )
                        $key = "'".$key."'";

                    if( is_array($value) ){
                        $arrayValueCount = count($value);
                        $value = "<b>array</b>".' (size='.$arrayValueCount.") ";

                        if($arrayValueCount > 0){
                            $value .= "[...]";
                        }
                        else{
                            $value .= "[]";
                        }
                    }
                    else
                        $value = self::dump($value);

                    $var .= "\n".$identity.$key.' => '.$value;
                } break;
            case 'object':
                $type = 'object';
                $prefix = "<b>$type</b>";
                $info = '('.get_class($value).') ';
                $color = '';
                $var = ''; break;
            case 'resource':
                $type = 'resource';
                $prefix = "<b>$type</b>";
                $info = '('.get_resource_type($value).")";
                $color = '';
                $var = ''; break;
            case 'NULL':
                $type = '';
                $prefix = '';
                $info = '';
                $color = '#3465a4';
                $var = 'null'; break;
            default:
                if( is_callable($value) ){
                    $type = 'callable';
                    $prefix = "<small>$type</small> ";
                    $info = '';
                    $color = '';
                    $var = '';
                }
                else{
                    $type = 'Unknown type';
                    $prefix = '';
                    $info = '';
                    $color = 'black';
                    $var = '';
                }
        }

        if(!self::$cli)
            $dump = $prefix.$info.'<span style="color: '.$color.'">'.$var.'</span>';
        else
            $dump = $type.$info.$var;

        return $dump;
    }
    private function generate($vars){
        self::$Output = "";

        if(!self::$cli)
            self::$Output = "<pre>";

        if(self::$title){
            if(!self::$cli)
                self::$Output .= '<b>';
            self::$Output .= self::$title;
            if(!self::$cli)
                self::$Output .= '</b>';
        }

        if(!self::$cli)
            self::$Output .= '<small>';
        self::$Output .= ' in call number: '.self::$call;
        if(!self::$cli)
            self::$Output .= '</small>';
        self::$Output .= "\n";

        // Trace
        if(self::$trace && self::$trace[0]['file'] and self::$trace[0]['line']){
            if(!self::$cli)
                self::$Output .= '<small>';

            $n = 1;
            foreach(self::$trace as $trace){
                self::$Output .= $trace['file'].':'.$trace['line'];

                if($n > 2){
                    break;
                }

                self::$Output .= "\n";

                $n++;
            }

            if(!self::$cli)
                self::$Output .= "</small>";
            self::$Output .= "\n";
        }

        self::$Output .= "\n";

        // Dump
        foreach($vars as $key => $value){
            if(@self::$labels[$key]){
                if(!self::$cli)
                    self::$Output .= '<b style="color:#7d7d7d">';
                self::$Output .= self::$labels[$key]."\n";
                if(!self::$cli)
                    self::$Output .= "</b>";
            }
            self::$Output .= self::dump($value)."\n";
        }

        self::$Output .= "\n";
        if(!self::$cli){
            self::$Output .= "</pre>";
            self::$Output .= "<style>pre{-moz-tab-size: 1; tab-size: 1;}</style>";
        }
    }
}
