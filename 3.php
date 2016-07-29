<?php
class Debug
{
   public static $debug = true;
   public static $print = true;
   public static $exit = true;

   public static $title; // string
   public static $trace; // array
   public static $vars; // array
      public static $labels; // array

   // Call
      // Configs
   public static $limit; // int
   public static $search; // string
      // Data
   public static $count = 1; // int

   // Output
   private $Output;

   public function __Construct(...$vars){
      if(self::$debug && ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' or $_SERVER['REMOTE_ADDR'] == '::1') ){
         if(empty($vars) && self::$vars)
            $vars = self::$vars;

         // Limit
         if(self::$limit === null)
            $limit = self::$count;
         else
            $limit = self::$limit;
         // Search
         if(self::$search === null)
            $search = self::$title;
         else
            $search = self::$search;

         // Catch
         if(self::$count >= $limit && $search == self::$title){
            if(!self::$trace){
               $trace = debug_backtrace();
               self::$trace = $trace[0];
            }

            $this->Generate($vars);

            if(self::$print)
               print $this->Output;

            self::$trace = null;

            if(self::$exit)
               exit;
         }

         if(self::$limit && self::$search){
            if($search == self::$title){
               self::$count++;
            }
         }
         else{
            self::$count++;
         }
      }
   }

   public static function Input(...$vars){
      self::$vars = $vars;
   }

   public static function Reset(){
      self::$count = 1;
      self::$limit = null;
      self::$search = null;
      self::$title = null;
      self::$labels = null;
   }

   public static function Dump($value){
      switch( gettype($value) ){
         case 'boolean':
            $prefix = "<small>boolean</small> ";
            $color = '#75507b';
            if($value)
               $var = 'true';
            else
               $var = 'false'; break;
         case 'integer':
            $prefix = "<small>int</small> ";
            $color = '#4e9a06';
            $var = $value; break;
         case 'double': // float
            $prefix = "<small>float</small> ";
            $color = "#f57900";
            $var = $value; break;
         case 'string':
            $prefix = "<small>string</small> ".'(length='.strlen($value).') ';
            $color = '#cc0000';
            $var = "'".$value."'"; break;
         case 'array':
            $prefix = "<b>array</b>".' (size='.count($value).") ";
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
                  $value = self::Dump($value);

               $var .= "\n".$identity.$key.' => '.$value;
            } break;
         case 'object':
            $prefix = "<b>object</b>".'('.get_class($value).') ';
            $color = '';
            $var = '';
            break;
         case 'resource':
            $prefix = '';
            $color = '';
				$var = ''; break;
         case 'NULL':
            $prefix = '';
            $color = '#3465a4';
            $var = 'null'; break;
         default:
            if( is_callable($value) ){
               $prefix = "<small>callable</small> ";
               $color = '';
               $var = '';
            }
            else{
               $prefix = 'Unknown type';
               $color = 'black';
               $var = '';
            }
      }

      return $prefix.'<span style="color: '.$color.'">'.$var.'</span>';
   }

   private function Generate($vars){
      $this->Output = "<pre>";
      if(self::$title)
         $this->Output .= '<b>'.self::$title.'</b>';
      $this->Output .= '<small> in call number: '.self::$count.'</small>';
      $this->Output .= "\n\n";
      if(self::$trace['file'] and self::$trace['line'])
         $this->Output .= '<small>'.self::$trace['file'].':'.self::$trace['line']."</small>\n";
      $this->Output .= "\n";
      foreach($vars as $key => $value){
         if(self::$labels[$key]){
            $this->Output .= '<b style="color:#7d7d7d">'.self::$labels[$key]."</b>\n";
         }
         $this->Output .= self::Dump($value)."\n";
      }
      $this->Output .= "</pre>";
      $this->Output .= "<style>pre{-moz-tab-size: 1; tab-size: 1;}</style>";
   }
}
