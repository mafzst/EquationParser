<?php
require_once('functions.php');

require_once('Equation.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {

	debug_var($equation = new Equation($_POST['equation']));

	debug_var($equation);
}
?>

<meta charset="UTF-8" />
<form action="" method="POST">
	<input type="text" name="equation" value="<?= isset($_POST['equation']) ? $_POST['equation'] : 'Entrez l\'équation ici'; ?>" required/>

	<input type="submit"/>
</form>
