<?php

  include('dev/yolk.php');
  $html = file_get_contents('index.html');
  $inputs_to_view = array(
    "title" => array("name" => "Hello World!","sub" => "Aliens!"),
    "Something" => "Additional text here",
    "listOfStuff" => array(
      array("name" => "Cindy","sex" => "female", "age" => "23"),
      array("name" => "John", "sex" => "male", "age" => "25"),
      array("name" => "orange", "sex" => "fruit", "age" => "0.5")
    ),
    "stuff" => array("red","blue","green"),
    "year" => "2016",
    "current_year" => "2016" 
  );
  $yolk = new Yolk;
  $yolk->template($html);
  $yolk->process($inputs_to_view);
  $yolk->view();

?>
