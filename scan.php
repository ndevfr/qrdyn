<?php include("header.php"); ?>
<div id="main">
<h1><?php _e("Scanner un QR-code"); ?></h1>
<div id="loadingMessage" hidden="">⌛ <?php _e("Chargement vidéo..."); ?></div>
<div class="qrscan"><canvas id="canvas" width="280" height="280"></canvas></div>
<a id="showVideo" class="buttonscan"><?php _e("Scanner"); ?></a>
<div id="outputMessage"></div>
<div id="errorMsg"></div>
<script type="text/javascript">
	var video = document.createElement("video");
    var canvasElement = document.getElementById("canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
	function drawLine(begin, end, color) {
		canvas.beginPath();
		a = (end.y - begin.y) / (end.x - begin.x);
		b = begin.y - a*begin.x;
		lx = end.x - begin.x;
		canvas.moveTo(begin.x, begin.y);
		canvas.lineTo(begin.x+lx/5, a*(begin.x+lx/5)+b);
		canvas.lineWidth = 4;
		canvas.strokeStyle = color;
		canvas.stroke();
		canvas.moveTo(end.x, end.y);
		canvas.lineTo(end.x-lx/5, a*(end.x-lx/5)+b);
		canvas.lineWidth = 4;
		canvas.strokeStyle = color;
		canvas.stroke();
	}	
const constraints = window.constraints = {
  audio: false,
  video: { facingMode: "environment" }
};
function handleSuccess(stream) {
	video.srcObject = stream;
	video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
	video.play();
	requestAnimationFrame(tick);
}
function handleError(error) {
	if (error.name === 'ConstraintNotSatisfiedError') {
		let v = constraints.video;
		errorMsg("<?php _e("La résolution n'est pas supporté par l'appareil."); ?>");
	} else if (error.name === 'PermissionDeniedError') {
		errorMsg("<?php _e("Les permissions pour utiliser la caméra n'ont pas été données."); ?>");
	}
	errorMsg("<?php _e("Erreur getUserMedia :") ?> ${error.name}", error);
}
function errorMsg(msg, error) {
	const errorElement = document.querySelector('#errorMsg');
	errorElement.innerHTML += `<p>${msg}</p>`;
	if (typeof error !== 'undefined') {
		console.error(error);
	}
}
async function init(e) {
	try {
		const stream = await navigator.mediaDevices.getUserMedia(constraints);
		handleSuccess(stream);
		e.target.disabled = true;
	} catch (e) {
		handleError(e);
	}
}
document.querySelector('#showVideo').addEventListener('click', e => init(e));
    function tick() {
		if (video.readyState === video.HAVE_ENOUGH_DATA) {
			loadingMessage.hidden = true;
			canvasElement.hidden = false;
			if(video.videoWidth > video.videoHeight){
				size = video.videoHeight;
				x0 = (video.videoWidth - video.videoHeight)/2;
				y0 = 0;
			} else {
				size = video.videoWidth;
				y0 = (video.videoHeight - video.videoWidth)/2;
				x0 = 0;
			}
			canvas.drawImage(video, x0, y0, size, size, 0, 0, canvasElement.width, canvasElement.height);
			var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
			var code = jsQR(imageData.data, imageData.width, imageData.height, {inversionAttempts: "dontInvert"});
			if (code) {
				drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
				drawLine(code.location.bottomLeftCorner, code.location.bottomRightCorner, "#FF3B58");
				drawLine(code.location.topLeftCorner, code.location.bottomLeftCorner, "#FF3B58");
				drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
				codeData = code.data;
				video.pause();
				message = "<strong><?php _e("QR code scanné !"); ?></strong><?php _e("Aller vers :"); ?> <a class='link' href='" + codeData + "'>" + codeData + "</a>";
				outputMessage.innerHTML = message;
			} else {
				outputMessage.innerHTML = "<em><?php _e("QR code non detecté."); ?></em>";
			}
		}
		requestAnimationFrame(tick);
	}
</script>
</div>
<?php include("footer.php"); ?>