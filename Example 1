<?php
\Debug::$limit = 3;
\Debug::$search = 'Odd';

$array = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
$arrayCount = count($array);
foreach($array as $key => $value){
   if($key == 0){
      \Debug::$tag = 'First index';
      new \Debug($array);
   }

   if( is_float($value / 2) ){
      \Debug::$tag = 'Odd';
      new \Debug($value);
   }
   else{
      \Debug::$tag = 'Even';
      new \Debug($value);
   }

   if($key == ($arrayCount - 1) ){
      \Debug::$tag = 'Last index';
      new \Debug($key);
   }
}

// Output "Odd in call number: 3" - int $value = 5
