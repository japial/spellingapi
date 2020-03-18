<!DOCTYPE html>
<html>

<head>
	<title>Spelling Bee</title>
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,900&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
		integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="<?= base_url() ?>assets/css/cirtificate.css">
</head>

<body>
	<div class="container cirtificate_area" id="html-content-holder" style="background-image: url(<?= base_url() ?>assets/img/ct-bg.png);">
		<div class="row" style="postion: relative; height: 100%;">
			<div class="col-md-2"></div>
			<div class="col-md-3">
				<div class="ct_logo">
					<img src="<?= base_url() ?>assets/img/logo.png">
				</div>
			</div>
			<div class="col-md-2"></div>
			<div class="col-md-3 c_level" id="certificateLevel"	data-certificate="<?php echo $user_id.'-level-'.$certificate_level.'.png'; ?>">
				<img style="width:100%;margin-top:185px" src="<?= base_url() ?>assets/img/level/<?= $certificate_level ?>.png">
			</div>
			<div class="col-md-2"></div>

			<div class="col-md-12 text-center">
				<div class="c_content">
					<h1><?= $player->name ?></h1>
					<h4><?=  stripslashes($player->school) ?></h4>
					<h4>Class: <?= $player->class_name ?>, <?= $player->division_name ?> Division</h4>
				</div>
			</div>

			<div class="col-md-12 text-center authorized">
				<img style="width:12%;margin-top:94px" src="<?= base_url() ?>assets/img/ct-auth.png">
			</div>

			<div class="row" style="postion: absolute; bottom: 0; left: 0;">
				<div class="col-md-12">
					<div class="ct_footer">
						<img src="<?= base_url() ?>assets/img/footer.png" style="width: 100%;">
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
	integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://files.codepedia.info/files/uploads/iScripts/html2canvas.js"></script>

<script>
	function uploadBaseImage(imageData, fileName) {
		var imgUploadData = imageData.replace(/^data:image\/png;base64,/, "");
		var formData = new FormData();
		formData.append('image_file', imgUploadData);
		formData.append('file_name', fileName);
		$.ajax({
			type: "POST",
			url: "https://spellingbee.champs21.com/api/board/save_certificate",
			data: formData,
			enctype: 'multipart/form-data',
			processData: false,
			contentType: false,
			success: function (data) {
				console.log(data);
			}
		});
	}
	$(document).ready(function () {
		var element = $("#html-content-holder"); // global variable
		var getCanvas; // global variable
		html2canvas(element, {
			onrendered: function (canvas) {
				$("#previewImage").append(canvas);
				getCanvas = canvas;
			}
		});
		setTimeout(function () {
			var imageData = getCanvas.toDataURL("image/png");
			var fileName = $("#certificateLevel").data('certificate');
			uploadBaseImage(imageData, fileName);
		}, 3000);

	});
</script>

</html>