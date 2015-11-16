var hoverCls = "file-hover";
var fileInput = $('[data-droppable-input=""]');
var fileImage = $('[data-droppable-image=""]');

var fileSelect = function (ev) {
	files = (ev.target && ev.target.files) || (ev.dataTransfer && ev.dataTransfer.files);
	if (files) {
		var reader = new FileReader();
		reader.onload = function(e) {
			var contents = e.target.result;
			fileImage.css("background-image", "url("+contents+")");
		};
		reader.readAsDataURL(files[0]);
		$("#uploadSuccess").hide();
		$("#uploadFail").hide();
		if ($('#deleteBtn').is(":visible")) {
			$('#deleteBtn').hide();
			$("#uploadBtn").show();
		}
		$("#uploadMsg").hide();
		$("#previewImg").removeClass("aspect-square-little");
		$("#previewImg").addClass("aspect-square");
	}
	else {
		$("#uploadSuccess").hide();
		$("#uploadFail").show();
		$("#uploadMsg").show();
		$("#previewImg").removeClass("aspect-square");
		$("#previewImg").addClass("aspect-square-little");
	}
};

fileInput[0].onchange = fileSelect;

var files="";

var validator = $("#uploadFileForm").validate({
	submitHandler: function(form, event) {
		uploadElemImg(event);
		return false;
	},
	highlight: function() {
		files = "";
		fileImage.css("background-image", "none");
		$("#uploadMsg").show();
		$("#previewImg").removeClass("aspect-square");
		$("#previewImg").addClass("aspect-square-little");
	},
	rules: {
		fileToUpload: {
			required: true,
			accept: "png|jpe?g",
			filesize: 1048576
		}
	},
	errorLabelContainer: "#uploadError"
});
