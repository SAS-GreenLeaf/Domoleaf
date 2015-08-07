<?php

include('header.php');

echo
	'<div class="panel">'.
      '<h2 class="panel-head">click or drag file</h2>'.
      '<div class="panel-content">'.
        '<label for="image">'.
          '<form action="" class="image-select" data-droppable="">'.
            '<input id="image" type="file" data-droppable-input=""/>'.
            '<i class="fa fa-camera fa-2x image-select__icon"></i>'.
            '<div class="image-select__message"></div>'.
            '<div class="bg-image aspect-square" '.
                 'style="background-image: url("http://placekitten.com/g/300/300");" data-droppable-image=""></div>'.
          '</form>'.
        '</label>'.
      '</div>'.
    '</div>';

?>