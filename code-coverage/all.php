<?php
declare(strict_types=1);
include('all-config.php');

if (!file_exists($file)) {
    echo 'File ' . $file . ' does not exist.';
}

$content = file_get_contents($file);

$doc = new DomDocument();
$doc->loadXML($content);
$xpath = new DOMXpath($doc);

$coverage = [];


$timeStampNode = $xpath->query('/coverage');
$timeStamp = $timeStampNode->item(0)->getAttribute('generated');
$timeStampText = date('d M Y @ H:i:s', (int)$timeStamp);


// get all file entries:
/** @var DOMNodeList $fileElements */
$fileElements = $xpath->query('/coverage/project/file/class');
/** @var \DOMElement $el */
foreach ($fileElements as $el) {
    $coverage[] = processElement($el);
}

$packageElements = $xpath->query('/coverage/project/package/file/class');
/** @var \DOMElement $el */
foreach ($packageElements as $el) {
    $coverage[] = processElement($el);
}

function processElement(DOMElement $el)
{
    $fullName  = $el->getAttribute('name');
    $name      = $el->getAttribute('name');
    $parts     = explode('\\', $name);
    $last      = $parts[count($parts)-1];
    $name      = $last;

    $namespace = $el->getAttribute('namespace');

    // dont use namespace element, doesnt work.
    array_pop($parts);
    $namespace = implode('\\', $parts);


    $fileName  = $el->parentNode->getAttribute('name');
    $metrics   = null;
    $node      = [];
    foreach ($el->childNodes as $child) {
        if ($child instanceof DOMElement) {
            $metrics = $child;
        }
    }
    if (!is_null($metrics)) {
        $methods     = $metrics->getAttribute('methods');
        $fields      = ['methods', 'conditionals', 'statements', 'conditionals', 'elements'];
        $node        = [
            'className' => $fullName,
            'namespace' => $namespace,
            'fileName'  => $fileName,
            'niceName' => $name,
        ];
        $node['url'] = http_build_query($node);
        foreach ($fields as $field) {
            $node[$field]             = intval($metrics->getAttribute($field));
            $node['covered' . $field] = intval($metrics->getAttribute('covered' . $field));
            if ($node['covered' . $field] == 0) {
                $node[$field . 'pct'] = 0;
            } else {
                $node[$field . 'pct'] = floor(intval($metrics->getAttribute('covered' . $field)) / intval($metrics->getAttribute($field)) * 100);
            }
            switch (true) {
                case ($node[$field . 'pct'] == 0):
                    $node[$field . 'class'] = '';
                    break;
                case ($node[$field . 'pct'] <= 80 && $node[$field . 'pct'] > 0):
                    $node[$field . 'class'] = 'danger';
                    break;
                case ($node[$field . 'pct'] > 80 && $node[$field . 'pct'] <= 99):
                    $node[$field . 'class'] = 'warning';
                    break;
                case ($node[$field . 'pct'] > 99):
                    $node[$field . 'class'] = 'success';
                    break;
            }

        }

    }
    return $node;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code coverage</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-sortable.css" rel="stylesheet">
    <link href="css/code-coverage.css" rel="stylesheet">

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
            <h4>
                Coverage generated at <?php echo $timeStampText; ?>
            </h4>
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                <tr>
                    <th>File</th>
                    <th>Methods</th>
                    <th>mpct</th>
                    <th>Statements</th>
                    <th>spct</th>
                    <th>Elements</th>
                    <th>epct</th>
                </tr>
                </thead>
                <?php
                $count = 0;

                $methodsCovered  = 0;
                $totalMethods    = 0;
                $methodsTotalPct = 0;

                $statCovered  = 0;
                $totalStat    = 0;
                $statTotalPct = 0;

                $elCovered  = 0;
                $totalEl    = 0;
                $elTotalPct = 0;
                ?>
                <tbody>
                <?php foreach ($coverage as $node): ?>

                    <?php
                    $methodsCovered += intval($node['coveredmethods']);
                    $totalMethods += intval($node['methods']);
                    $methodsTotalPct += intval($node['methodspct']);

                    $statCovered += intval($node['coveredstatements']);
                    $totalStat += intval($node['statements']);
                    $statTotalPct += intval($node['statementspct']);

                    $elCovered += intval($node['coveredelements']);
                    $totalEl += intval($node['elements']);
                    $elTotalPct += intval($node['elementspct']);
                    $count++;
                    ?>
                    <?php
                    if (intval($node['methods']) > 0 || intval($node['statements']) > 0 || intval($node['elements']) > 0):
                        ?>

                        <tr>
                            <td data-value="<?php echo $node['className'];?>"><small><?php echo $node['namespace']; ?>\</small><a href="all-file.php?<?php echo $node['url'];?>"><?php echo $node['niceName']; ?></a>
                            </td>
                            <td data-value="<?php echo $node['coveredmethods'];?>" class="<?php echo $node['methodsclass']; ?>"><?php echo $node['coveredmethods']; ?> of <?php echo $node['methods']; ?></td>
                            <td data-value="<?php echo $node['methodspct'];?>" class="<?php echo $node['methodsclass']; ?>"><?php echo $node['methodspct']; ?>%</td>
                            <td class="<?php echo $node['statementsclass']; ?>"><?php echo $node['coveredstatements']; ?>
                                of <?php echo $node['statements']; ?></td>
                            <td data-value="<?php echo $node['statementspct'];?>" class="<?php echo $node['statementsclass']; ?>"><?php echo $node['statementspct']; ?>%</td>
                            <td class="<?php echo $node['elementsclass']; ?>"><?php echo $node['coveredelements']; ?> of <?php echo $node['elements']; ?></td>
                            <td data-value="<?php echo $node['elementspct'];?>" class="<?php echo $node['elementsclass']; ?>"><?php echo $node['elementspct']; ?>%</td>
                        </tr>
                    <?php
                    endif;
                    ?>
                <?php endforeach; ?>
                <?php
                $count = $count == 0 ? 1 : $count;
                $pcts = [];
                $class = [];
                $pcts['methods'] = round(($methodsTotalPct / $count), 2);;
                $pcts['stats']    = round(($statTotalPct / $count), 2);
                $pcts['elements'] = round(($elTotalPct / $count), 2);

                foreach($pcts as $field => $value) {
                    switch (true) {
                        case ($value == 0):
                            $class[$field] = '';
                            break;
                        case ($value <= 80 && $value > 0):
                            $class[$field] = 'danger';
                            break;
                        case ($value > 80 && $value <= 99):
                            $class[$field] = 'warning';
                            break;
                        case ($value > 99):
                            $class[$field] = 'success';
                            break;
                    }
                }



                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td><em>Sum</em></td>
                    <td class="<?php echo $class['methods'];?>"><?php echo $methodsCovered; ?> of <?php echo $totalMethods; ?></td>
                    <td data-value="<?php echo $pcts['methods'];?>" class="<?php echo $class['methods'];?>"><?php echo $pcts['methods'] ?>%</td>
                    <td class="<?php echo $class['stats'];?>"><?php echo $statCovered; ?> of <?php echo $totalStat; ?></td>
                    <td class="<?php echo $class['stats'];?>"><?php echo $pcts['stats']; ?>%</td>
                    <td class="<?php echo $class['elements'];?>"><?php echo $elCovered; ?> of <?php echo $totalEl; ?></td>
                    <td class="<?php echo $class['elements'];?>"><?php echo $pcts['elements']; ?>%</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.min.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/bootstrap-sortable.js"></script>
</body>
</html>