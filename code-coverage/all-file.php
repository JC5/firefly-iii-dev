<?php
$fileGet = isset($_GET['fileName']) ? $_GET['fileName'] : false;
if (!file_exists($fileGet)) {
    die('No file.');
}
$lines    = explode("\n", file_get_contents($fileGet));
$coverage = [];
// read XML file:
include('all-config.php');
$content = file_get_contents($file);

$doc = new DomDocument();
$doc->loadXML($content);
$xpath = new DOMXpath($doc);

// get all file entries on the class name:
/** @var DOMNodeList $fileElements */
$classElements = $xpath->query('/coverage/project/file/class[@name="' . $_GET['className'] . '"]');
if ($classElements->length == 0) {
    // try again with other query:
    $classElements = $xpath->query('/coverage/project/package/file/class[@name="' . $_GET['className'] . '"]');
    if ($classElements->length == 0) {
        die('No class element');
    }
}
$classElement = $classElements->item(0);

// find the correct one (namespace, remember!)
if($classElements->length > 1) {
	foreach($classElements as $el) {
		$nameSpace = $el->getAttribute('namespace');
		if($nameSpace == $_GET['namespace']) {
			$classElement = $el;
		}
	}
}

$parent       = $classElement->parentNode;
$lineNodes    = $parent->childNodes;
/** @var     \DOMElement $node */
foreach ($lineNodes as $node) {
    if ($node instanceof DOMElement && $node->tagName == 'line') {
        $nr            = intval($node->getAttribute('num'));
        $count         = intval($node->getAttribute('count'));
        $coverage[$nr] = $count;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code coverage: <?php echo $_GET['className']; ?></title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-condensed table-bordered" style="font-family: monospace;font-size:12px;">
                <?php foreach ($lines as $index => $line): ?>
                    <?php $lineNr = $index + 1; ?>
                    <?php if (isset($coverage[$lineNr]) && $coverage[$lineNr] > 0) { ?>
                        <tr class="success">
                    <?php } else {
                        if (isset($coverage[$lineNr]) && $coverage[$lineNr] == 0) { ?>
                            <tr class="danger">
                        <?php } else { ?>
                            <tr>
                        <?php }
                    } ?>
                    <td style="width:30px;"><small><?php echo $index+1;?></small></td>
                    <?php if (trim($line) == '') { ?>
                        <td>&nbsp;</td>
                    <?php } else { ?>
                        <td><?php echo str_replace([' '], ['&nbsp;'], htmlspecialchars($line)); ?></td>
                    <?php } ?>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
</body>
</html>

