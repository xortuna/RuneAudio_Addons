<?php
require_once('addonshead.php');

$cmd = $_POST['cmd'];
$close = strpos($cmd, 'addo') ? '/' : 'addons.php'; 
?>

<script>
window.addEventListener('resize', function() {
	window.scrollTo(0,document.body.scrollHeight);
});
	
/*var h0 = document.body.scrollHeight;
var h1;
setInterval( function() {
	h1 = document.body.scrollHeight;
	if ( h1 > h0 ) window.scrollTo( 0, h1 );
	h0 = h1;
}, 1000 );*/
</script>

<?php
echo '<div class="container">'
	.'<h1>ADDONS TERMINAL</h1><a id="close" href="'.$close.'"><i class="fa fa-times fa-2x"></i></a>'
	.'<p>Please wait until finished.</p>';

function bash($cmd) {
	while (@ ob_end_flush()); // end all output buffers if any

	$proc = popen("$cmd 2>&1", 'r');

	while (!feof($proc)) {
		$std = fread($proc, 4096);
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;6m/', '<a class="cc">', $std); // bar
		$std = preg_replace('/.\\[38;5;0m.\\[48;5;3m/', '<a class="ky">', $std); // info, yesno
		$std = preg_replace('/.\\[38;5;7m.\\[48;5;1m/', '<a class="wr">', $std); // warn
		$std = preg_replace('/.\\[38;5;6m.\\[48;5;0m/', '<a class="ck">', $std); // tcolor
		$std = preg_replace('/.\\[38;5;6m/', '<a class="ck">', $std);            // lcolor
		$std = preg_replace('/.\\[0m/', '</a>', $std);                           // reset color
		echo "$std";
		@ flush();
	}

	pclose($proc);
}

echo '<pre>';
bash($cmd);
echo '</pre>';
?>
</div>

</body>
</html>
