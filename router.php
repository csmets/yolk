<?php

  include('dev/yolk.php');
  $html = file_get_contents('index.html');
  $inputs_to_view = array(
    "title" => "Hello World!",
    "Something" => "Additional text here",
    "listOfStuff" => array("apple","pear","orange")
  );
  $yolk = new Yolk;
  $yolk->template($html);
  $yolk->process($inputs_to_view);
  $yolk->view();

?>
