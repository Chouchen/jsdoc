<?php 

//Chemin vers le fichier JS à documenter
define('JS_FILE','C:\chemin\fichier.js');
//Chemin du fichier html de sortie
define('HTML_OUT','C:\chemin\doc.html');

header('Content-Type: text/html; charset=utf-8');
$name = basename(JS_FILE);
$lines = file(JS_FILE);
$methods = array();
for($i=0;$i<count($lines);$i++){
	$line = $lines[$i];
	$method = array();
	preg_match("/\s*(.*)\s*[:=]\s*function\s*\((.*)\)/", $line, $matches);
	if(count($matches)==0) continue;
	$method['name'] = $matches[1];
	$method['args'] = explode(',',$matches[2]);
	$method['signature'] = trim(preg_replace("/\s*[:=]\s*function\s*\(/", '(', $matches[0])) ;
	$method['static'] = substr($method['name'],0,2)=='$.'?true:false;
	$method['description'] =  'Aucune description';
	if(!isset($lines[$i-1]) || substr(trim($lines[$i-1]),0,2) != '//') continue;
		$method['description'] = substr(trim($lines[$i-1]),2);
		for($u=0;$u<count($method['args']);$u++):
			$arg = $method['args'][$u];
			$method['description'] = str_replace("'".$arg."'","<code>« ".$arg." »</code>",$method['description']);
			
		endfor;
	$methods[] = $method;
}
ob_start(); 
?>

<!doctype html>
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Documentation <?php echo $name ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		
html {
    color: #222;
    font-size: 1em;
    line-height: 1.4;
}

::-moz-selection {
    background: #b3d4fc;
    text-shadow: none;
}

::selection {
    background: #b3d4fc;
    text-shadow: none;
}

hr {
    display: block;
    height: 1px;
    border: 0;
    border-top: 1px solid #ccc;
    margin: 1em 0;
    padding: 0;
}

audio,
canvas,
iframe,
img,
svg,
video {
    vertical-align: middle;
}

fieldset {
    border: 0;
    margin: 0;
    padding: 0;
}

textarea {
    resize: vertical;
}

.list-group a{
	display: block;
}

body {
    font: 16px/26px Roboto,Helvetica, Helvetica Neue, Arial;
     color: #333333;
}

h1,h2,h3{
    font-weight: 300;
}


.bold{
    font-weight: bold;
}

ul{
    padding:0;
    margin:0;
}

a{
    color:#1D8293;
}


	</style>
</head>
<body>
    <h1>Javascript :<?php echo $name ?></h1>
    <p>Veuillez trouver ci dessous les différentes fonctions javascript du fichier <?php echo $name ?></p>
    
    

<ul id="summary">
<?php
$type = '';
//Summary
$i =0;
foreach($methods as $method):
	?>
	<li>
	<?php
	if($type != $method['static']){ ?>
		<h2>
			<?php echo 'METHODES '.($method['static']?'':'NON ').'STATIQUES'; ?>
        </h2>
		<?php
		$type = $method['static'];
	}
	?>
        <a href="#bloc_<?php echo $i; ?>">
			<strong><?php echo $method['name']; ?></strong> <i><?php echo implode(',',$method['args']); ?></i> - <small><?php echo $method['description']; ?></small>
        </a>
     </li>
	<?php
	$i++;
endforeach;
?>
</ul>

<ul id="detail">
<?php
//Descriptions
$i =0;
foreach($methods as $method):
	
	?>
	<li id="bloc_<?php echo $i; ?>">
		<div class="tab-pane fade active in" id="home<?php echo $i; ?>">
			<h4><?php echo $method['name']; ?><small><?php echo $method['static']?' <span class="label label-warning">Méthode statique</span>':''; ?></small></h4>
			<?php if(count($method['args'])>0){ ?> Argument(s) : <i class="label label-primary"><?php echo implode(',',$method['args']); ?></i><?php } ?><br/><br/>
			<p><?php echo $method['description']; ?></p>
		</div>
		<div class="tab-pane fade" id="sample<?php echo $i; ?>">
		<pre>
			<code><?php echo (!$method['static']?"$('#monElement').":"").$method['signature']; ?></code>
		</pre>
		
		</div>
	</li>

	<?php
	$i++;
endforeach;
?>
</ul>

 </body>
 </html>

<?php
$xml = ob_get_clean();
file_put_contents(HTML_OUT,$xml);
echo 'Doc générée dans '.HTML_OUT;
?>