<?php

  include('dev/yolk.php');
  $html = file_get_contents('index.html');
  $inputs_to_view = array(
    "title" => array("name" => "Hello World!","sub" => "Aliens!"),
    "Something" => "Additional text here",
    "listOfStuff" => array(
      array("name" => "apple"),
      array("name" => "pear"),
      array("name" => "orange")
    ),
    "stuff" => array("red","blue","green")
  );
  $yolk = new Yolk;
  $yolk->template($html);
  $yolk->process($inputs_to_view);
  $yolk->view();

?>
