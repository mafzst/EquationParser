<?php
require_once('lib/functions.php');

require_once('lib/EquationParser.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	$equation = new npsi\EquationParser($_POST['equation']);

	var_dump($equation->get_hashCode());
}
?>

<meta charset="UTF-8" />
<form action="" method="POST">
	<input type="text" name="equation" value="<?= isset($_POST['equation']) ? $_POST['equation'] : 'Entrez l\'équation ici'; ?>" required/>

	<input type="submit"/>
</form>
