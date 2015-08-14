var hoverCls = "file-hover";
var $fileDrop = $('[data-droppable=""]');
var $fileInput = $('[data-droppable-input=""]');
var $fileImage = $('[data-droppable-image=""]');

var fileHover = function (ev) {
	ev.stopPropagation();
	ev.preventDefault();
	if (ev.type === "dragover") {
		$fileDrop.addClass(hoverCls);
	} else {
		$fileDrop.removeClass(hoverCls);
	}
};

var fileSelect = function (ev) {
	fileHover(ev);
	files = ev.target.files || ev.dataTransfer.files;
	var reader = new FileReader();

	reader.onload = function (ev) {
		$fileImage.css("background-image", "url("+ev.target.result+")");
	};
	reader.readAsDataURL(files[0]);
};

$fileDrop[0].ondragover = fileHover;
$fileDrop[0].ondragleave = fileHover;
$fileDrop[0].ondrop = fileSelect;
$fileInput[0].onchange = fileSelect;

var files="";

$("#image").on("change", function(event){
	files = event.target.files;
});

$("#formUpload").on("submit", function(event){
	event.stopPropagation();
	event.preventDefault();
	if (files != ""){
		var data = new FormData();
		data.append("file", files[0]);
		data.append("device", $("#iddevice").val());
		data.append("userid", $("#userid").val());
		$.ajax({
			url: "/templates/default/form/form_custom_upload_device.php",
			type: "POST",
			data: data,
			processData: false,
			contentType: false,
			success: function(data, textStatus){
				if (data == "0"){
					$("#uploadSuccess").hide();
					$("#uploadFail").show();
				} else {
					$("#uploadFail").hide();
					$("#uploadSuccess").show();
					$('#deleteBtn').show();
					$('#widget-bg-'+$("#iddevice").val()).css("background-image", "url(\""+data+"\")");
				}
			},
		});
	}
	else {
		$("#formUpload").click();
	}
});

$("#deleteBtn").on("click", function(event){
	$.ajax({
		url: "/templates/default/form/form_custom_delete_device.php",
		type: "POST",
		data: "device="+$("#iddevice").val()+"&userid="+$("#userid").val(),
		success: function(data, textStatus){
				$("#uploadSuccess").show();
				$("#uploadFail").hide();
				$('#deleteBtn').hide();
				$fileImage.css("background-image", "none");
				$('#widget-bg-'+$("#iddevice").val()).css("background-image", "none");
			}
	});
});

