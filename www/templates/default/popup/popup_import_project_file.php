<?php

include('header.php');

$request = new Api();
$result = $request->send_request();

echo '<div class="center">'.
        '<link rel="stylesheet" href="/css/inputfile.css">'.
        '<script src="/js/inputfile.js"></script>'.
        '<div class="controls center">'.
          '<div class="input-file-container">'.
            '<h3 id="project-file-name">'._('No file selected').'</h3>'.
            '<input class="input-file" id="project-file-input" type="file" accept=".sql">'.
            '<label for="project-file-input" class="input-file-trigger btn btn-primary" tabindex="0"><span class="glyphicon glyphicon-share"></span> '._('Choose a project file to import').'</label>'.
          '</div>'.
          '<button class="btn btn-success" onclick="ImportProjectFromFile()">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button>'.
          '<button class="btn btn-danger" onclick="popup_close()">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button>'.
        '</div>'.
      '</div>'.
      '<script>'.
        'function ImportProjectFromFile() {'.
          'var file = document.getElementById("project-file-input");'.
          'if (file.files.length) {'.
            'var reader = new FileReader();'.
            'reader.onload = function (e) {'.
              '$.ajax({'.
                'type: "POST",'.
                'url: "/form/form_import_project_from_file.php",'.
                'data: {'.
                  'content: e.target.result'.
                '},'.
                'success: function (res) {'.
                  'popup_close();'.
                  'document.location.reload()'.
                '}, error: function (err) {'.
                  'console.log(err);'.
                '}'.
              '});'.
            '};'.
            'reader.readAsText(file.files[0]);'.
          '}'.
        '}'.
      '</script>';
?>
