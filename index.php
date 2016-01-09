<?php
require_once('functions.php');

require_once('Equation.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$equation = new Equation($_POST['equation']);

	$string = "";

	foreach ($equation->variables as $var => $value) {
		$string .= $var . "?";
		$string .= $value['expression'] . '&' . $value['min'] . '&' . $value['max'] . '&' . $value['pas'] . '@';
	}
	var_dump($string);
}
?>

<meta charset="UTF-8" />
<form action="" method="POST">
	<input type="text" name="equation" value="<?= isset($_POST['equation']) ? $_POST['equation'] : 'Entrez l\'équation ici'; ?>" required/>

	<input type="submit"/>
</form>
