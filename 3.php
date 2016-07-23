<?php
class Debug
{
   public static $debug = true;
   public static $backtrace = true;
   public static $exit = true;

   // Call
      // Configs
   public static $limit; // int
   public static $search; // string
      // Data
   private static $count = 1; // int

   // Trace
   public static $trace;

   // Output
   public static $tag; // string
   public static $print = true; // bool
   private $Output;

   public function __Construct(...$vars){
      if(self::$debug && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' or $_SERVER['REMOTE_ADDR'] == '::1') ){
         // Limit
         if(self::$limit === null)
            $limit = self::$count;
         else
            $limit = self::$limit;
         // Search
         if(self::$search === null)
            $search = self::$tag;
         else
            $search = self::$search;

         // Catch
         if(self::$count >= $limit && $search == self::$tag){
            if(self::$backtrace){
               if(!self::$trace){
                  $trace = debug_backtrace();
                  self::$trace = $trace[0];
               }
            }

            $this->Generate($vars);

            if(self::$print)
               print $this->Output;

            if(self::$backtrace)
               self::$trace = null;

            if(self::$exit)
               exit;
         }

         if(self::$limit && self::$search){
            if($search == self::$tag){
               self::$count++;
            }
         }
         else{
            self::$count++;
         }
      }
   }

   public static function Reset(){
      self::$count = 1;
      self::$limit = null;
      self::$search = null;
      self::$tag = null;
   }

   public static function Dump($var){
      switch( gettype($var) ){
         case 'boolean':
            $prefix = "<small>boolean</small> ";
            $color = '#75507b';
            if($var)
               $var = 'true';
            else
               $var = 'false'; break;
         case 'integer':
            $prefix = "<small>int</small> ";
            $color = '#4e9a06'; break;
         case 'double': // float
            $prefix = "<small>float</small> ";
            $color = "#f57900";
            break;
         case 'string':
            $prefix = "<small>string</small> ".'(length='.strlen($var).') ';
            $color = '#cc0000';
            $var = "'".$var."'"; break;
         case 'array':
            $prefix = "<b>array</b>".' (size='.count($var).") ";
            $color = '';
            $array = $var;
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
                  $value = self::Dump($value);

               $var .= "\n".$identity.$key.' => '.$value;
            } break;
         case 'object':
            $prefix = "<b>object</b>".'('.get_class($var).') ';
            $color = 'black';
            $var = '';
            break;
         case 'resource':
            $prefix = '<b>resource</b>';
            $color = 'black'; break;
         case 'NULL':
            $prefix = '';
            $color = '#3465a4';
            $var = 'null'; break;
         default:
            if( is_callable($var) ){
               $prefix = "<small>callable</small> ";
               $color = '';
            }
            else{
               $prefix = 'Unknown type';
               $color = 'black';
					$var = '?';
            }
      }

      return $prefix.'<span style="color: '.$color.'">'.$var.'</span>';
   }

   private function Generate($vars){
      $this->Output = "<pre>";
      if(self::$tag)
         $this->Output .= '<b>'.self::$tag.'</b>';
      $this->Output .= '<small> in call number: '.self::$count.'</small>';
      $this->Output .= "\n\n";
      if(self::$trace['file'] and self::$trace['line'])
         $this->Output .= '<small>'.self::$trace['file'].':'.self::$trace['line']."</small>\n";
      $this->Output .= "\n";
      foreach($vars as $key => $value)
         $this->Output .= self::Dump($value)."\n";
      $this->Output .= "</pre>";
      $this->Output .= "<style>pre{-moz-tab-size: 1; tab-size: 1;}</style>";
   }
}
