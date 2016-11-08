<?php
//\Debug::$to = 5;
\Debug::$search = 'Last index';

$array = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
$arrayCount = count($array);
foreach($array as $key => $value){
   if($key == 0){
      \Debug::$title = 'First index';
      new \Debug($array);
   }

   if( is_float($value / 2) ){
      \Debug::$title = 'Odd';
      new \Debug($value);
   }
   else{
      \Debug::$title = 'Even';
      new \Debug($value);
   }

   if($key == ($arrayCount - 1) ){
      \Debug::$title = 'Last index';
      new \Debug($key);
   }
}

// Output "Last index in call number: 12" - $key int 9
