<?php

/*
  @author Clyde Smets clyde.smets@gmail.com

  @desc Yolk is a templating engine for PHP

  @TODO Check if parent has loop

   ? - Make standard variables not affect nested variables inside loops

   - Nested loops
   - if conditionals

*/

class Yolk{

  private static $_template;

  /*
    Store the template/html code in a global variable which will be
    used to replace template variables with according inputs
  */
  public function template($html){
    $this->_template = $html;
  }

  /*
    Scan through template to find yolk variables to replace them with
    assigned inputs.
  */
  public function process($inputs){

    // Find all yolk types [{*}] and return them into $matches
    $regex = '/\[{(.*?)}\]/';
    preg_match_all($regex, $this->_template, $matches);
    //--------------------------------------------------------

    // Loop through all matches and replace match with input
    for($i = 0; $i < count($matches[1]); $i++){
      $variable = $matches[0][$i];
      $key = $matches[1][$i];
      $value = $inputs[$key];

      //Checks if the key triggers back any value and if so replace it with value
      if($value){
        $this->_template = str_replace($variable,$value,$this->_template);
      }

      //If the key doesn't return a value check to see if it's an object instead
      elseif($this->_has_key_pointer($key) == true){
        $value = $this->_object_variable($inputs,$key);
        $this->_template = str_replace($variable,$value,$this->_template);
      }
    }

    // Run Yolk foreach method
    $this->_process_loops($inputs);

    $this->_process_conditionals($inputs);
  }

  private function _has_key_pointer($string){
    if(strpos($string,'.') == true){
      return true;
    }else{
      return false;
    }
  }

  private function _object_variable($object, $key){
    $params = explode('.',$key);
    $index = $params[0];
    $value = $params[1];
    return $object[$index][$value];
  }

  /*
    find Yolk loop methods used in the template and process them
  */
  private function _process_loops($inputs){

    $regex_loop = '/(?s)\[loop {(.*)}\](.*)\[\/loop]/';
    preg_match_all($regex_loop, $this->_template, $loop_container);

    //Storage variables which are used for str_replace
    $element_original_html = '';
    $element_new_html = '';

    for($loop_index = 0; $loop_index < count($loop_container[0]); $loop_index++){

      $param = $loop_container[1][$loop_index];
      $body = $loop_container[2][$loop_index];

      if($param != null){
        $element_original_html = $loop_container[0][$loop_index];
        $element_new_html = $this->_loop($inputs, $body, $param);
      }
      $this->_template = str_replace($element_original_html, $element_new_html, $this->_template);
    }
  }

  /*
    Loop through input elements and replace yolk variables with values
  */
  private function _loop($inputs, $loop_contents, $condition){

    $param = explode('=>', $condition);
    $array = $inputs[trim($param[0])];

    $element_new_html = '';

    if (count($array) > 0){
      foreach($array as $item){
        $new_html = $loop_contents;
        //Find all variables to replace with input's item values
        $regex_variables = '/\{{(.*?)}\}/';
        preg_match_all($regex_variables, $loop_contents, $variables);
        for ($variable=0; $variable < count($variables[1]); $variable++) {
          if (gettype($item) == 'array' && $this->_has_key_pointer($variables[1][$variable]) == true){
            $split_object = explode('.',$variables[1][$variable]);
            $assigned_key = $split_object[0];
            $temp_data = array($assigned_key => $item);
            $value = $this->_object_variable($temp_data, $variables[1][$variable]);
            $new_html = str_replace($variables[0][$variable],$value,$new_html);
          }else{
            if ($param[1] == $variables[1][$variable]){
              $new_html = str_replace($variables[0][$variable],$item,$loop_contents);
            }
          }
        }
        $element_new_html .= $new_html;
      }
    }

    return $element_new_html;
  }

  public function _process_conditionals($inputs){

  }

  public function _conditionals(){

  }

  // Load up the template for viewing.
  public function view(){
    echo $this->_template;
  }

}

 ?>
