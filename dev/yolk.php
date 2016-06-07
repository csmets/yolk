<?php

/*
  @author Clyde Smets clyde.smets@gmail.com

  @desc Yolk is a templating engine for PHP

  @TODO Have input object able to access children eg: object.child

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
      if($value){
        $this->_template = str_replace($variable,$value,$this->_template);
      }
    }

    // Run Yolk foreach method
    $this->_process_loops($inputs);
  }

  private function _object_variable($object, $key){

  }

  /*
    find Yolk loop methods used in the template and process them
  */
  private function _process_loops($inputs){
    $DOM = new DOMDocument();
    $DOM->loadHTML($this->_template);
    $elements = $DOM->getElementsByTagName('div');

    //Storage variables which are used for str_replace
    $element_original_html = '';
    $element_new_html = '';

    foreach($elements as $element){

      //Get the condition stored in the attribue loop="array>value"
      $attr_loop_cond = $element->getAttribute('loop');

      if($attr_loop_cond != null){
        $element_original_html = DOMinnerHTML($element);
        $element_new_html .= $this->_loop($inputs, $attr_loop_cond);
      }

    }
    $this->_template = str_replace($element_original_html, $element_new_html, $this->_template);
  }

  /*
    Loop through input elements and replace yolk variables with values
  */
  private function _loop($inputs, $condition){

    $param = explode('>', $condition);
    $array = $inputs[trim($param[0])];

    $element_new_html = '';

    if (count($array) > 0){
      foreach($array as $item){
        $new_html = '';

        //Find all containers for iteration
        $regex_containers = '/\{{(.*?)}\}/';
        preg_match_all($regex_containers, $this->_template, $containers);

        //Loop through all containers and append it to the new_html var
        for ($object=0; $object < count($containers[1]); $object++) {
          $new_html .= $containers[1][$object];
        }

        //Find all variables to replace with input's item values
        $regex_variables = '/\[{(.*?)}\]/';
        preg_match_all($regex_variables, $new_html, $variables);

        for ($variable=0; $variable < count($variables[1]); $variable++) {
          if ($param[1] == $variables[1][$variable]){
            $new_html = str_replace($variables[0][$variable],$item,$new_html);
          }
        }

        $element_new_html .= $new_html;
      }
    }

    return $element_new_html;
  }

  // Load up the template for viewing.
  public function view(){
    echo $this->_template;
  }

}

function DOMinnerHTML($element)
{
   $innerHTML = "";
   $children = $element->childNodes;
   foreach ($children as $child)
   {
      $tmp_dom = new DOMDocument();
      $tmp_dom->appendChild($tmp_dom->importNode($child, true));
      $innerHTML.=trim($tmp_dom->saveHTML());
   }
   $innerHTML=html_entity_decode(html_entity_decode($innerHTML));
   return $innerHTML;
}

 ?>
