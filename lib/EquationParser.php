<?php

namespace npsi;

require_once('functions.php');

/*FIXME Problème au niveau de l'attribution des parenthèses lors de déplacements
	de variables pour les résolutions.
*/
class EquationParser
{
	private $equation;	//string
	private $inconnue;	//string
	private $expression;//string
	private $question;	//string
	private $reponse;
	public $variables = array();

	public function __construct($equation)
	{
		$this->equation = htmlentities(clean_spaces($equation));
		//$this->parser = $parser;

		return $this->parse();
	}

	public function parse() {
		$regex = "/(^[a-z])(([0-9]+|[a-z]+|\b)+)/i";

		$membres = explode("=", $this->equation);

		$variable = $membres[0];
		$this->inconnue = $variable;
		$this->expression = $membres[1];

		$this->variables[$variable]['expression'] = $this->expression;

		$this->identify_blocs($this->expression );

		$this->find_vars();

		foreach ($this->variables as $var => $array) {
			if(is_null($array['expression'])) {
				$this->variables[$var]['expression'] = clean_spaces($this->resolve_expression($var, $this->blocs, $this->inconnue));
				//DUMMY CONTENT
				$this->variables[$var]['min'] = 1;
				$this->variables[$var]['max'] = 100;
				$this->variables[$var]['pas'] = 10;
			}
		}

		return $this;
	}

	private function identify_blocs($string) {
		$i = 0;
		$bloc = "";
		$count = 0;
		$total = 0;

		while(isset($string[$i])) {
			$buff = $string[$i];
			switch($buff) {
				case '(':
					if($count==0 and $bloc !== "") {
						$this->blocs[] = $bloc;
						$bloc = "";
					}

					$count++;
					$total ++;
					break;
				case ')':
					$count--;

					if($count == 0) {
						$bloc = $bloc . $buff;
						$this->blocs[] = $bloc;
						$bloc = "";
						$buff = "";
					}
					break;
				default:
					break;
			}

			$bloc = $bloc . $buff;

			$i++;
		}

		if($bloc !== "") {
			$this->blocs[] = $bloc;
			$bloc = "";
		}

		if(count($this->blocs) == 1) {
			$this->blocs = $this->blocs[0];
		}
	}

	private function find_vars() {

		$array = preg_split('/\*|\/|\+|-|\)|\(/i', $this->expression);

		foreach ($array as $variable) {
			if(!isset($this->variables[$variable]) && $variable !== "") {
				$this->variables[$variable]['expression'] = null;
			}
		}

		return $this;
	}

	private function resolve_expression($variable, $expression, $inconnue) {
		if(is_array($expression)) {
			$finded = 0;
			$index = -1;

			foreach ($expression as $i => $bloc) {
				if(stripos($bloc, $variable)) {
					$index = $i;
					$finded++;
				}
			}

			switch($finded) {
				case 0:
					return null;
					break;
				case 1:			// On ne fait rien on passe à la suite
					break;
				default:		// Plusieurs instances de la variable pas possible
					return null;
					break;
			}

			if(count($expression) - 1 === $index) {		// Dernier bloc
				$signe = $expression[$index -1];
				$newexp = $expression[$index];
				$debut = null;

				for($i = 0; $i < $index - 1; $i++) {
					$debut .= $expression[$i];
				}

				if($this->is_commutative($signe)) {
					$signe = $this->invert_sign($signe);

					$newincon = $inconnue . $signe . '(' . $debut . ')';

					debug_var($newexp);

				} else {
					$newincon = '(' . $debut . ')' . $signe . $inconnue;
				}
				return $this->resolve_expression($variable, $newexp, $newincon);

			} else {
				//TODO Déplacer le bloc mal placé
			}

		} else {
			$pos = stripos($expression, $variable);
			$debut = "";
			$fin = "";

			if((strlen($variable) + $pos == strlen($expression)) or (strlen($variable) + $pos + 1 == strlen($expression) && $expression[strlen($variable) + $pos] === ')')) { // Dernière variable

				for($i = 0; $i < $pos - 1; $i++) {
					$debut .= $expression[$i];
				}
				$signe = $expression[$pos - 1];

				if($this->is_commutative($signe)) {
					$signe = $this->invert_sign($signe);
					if(preg_match('/\*|\/|\+|-/i', $debut)) {
						return $inconnue . $signe . '(' . $debut . ')';
					}
					return $inconnue . $signe . $debut;
				} else {
					return $debut . $signe . '(' . $inconnue . ')';
				}

			} else {
				$signe = $expression[$pos + strlen($variable)];

				if($this->is_commutative($signe)) {

					for($i = 0; $i < $pos; $i++) {
						$debut .= $expression[$i];
					}

					for($i = $pos + strlen($variable) + 1; $i < strlen($expression); $i++) {
						$fin .= $expression[$i];
					}

					$newexp = $debut . $fin . $signe . $variable;

					return $this->resolve_expression($variable, $newexp, $inconnue);
				} else {
					//TODO Déplacer bloc non commutatif
					return null;
				}
			}
		}
	}

//TODO Refactor tout ce qui suit !! ;)
// 	public function set_values($values)
// 	{
// 		if(count($values) === count($this->variables))
// 		{
// 			$combine = [];
// 			foreach($values as $val)
// 			{
// 				$temp = [];
//
// 				for($i = (Integer)$val['min']; $i <= $val['max']; $i = $i + $val['pas'])
// 				{
// 					$temp[] = $i;
// 				}
// 				$combine[] = $temp;
// 			}
// 			$this->variables = array_combine($this->variables, $combine);
// 		}
// 	}
//
// 	public function random_expression()
// 	{
// 		$response = $this->expression;
//
// 		foreach ($this->variables as $var => $vals) {
// 			$val = isset($vals[array_rand($vals)]) ? $vals[array_rand($vals)] : 0;
// 			$pattern = '/' . $var . '\s/';
// 			$response = preg_replace($pattern, $val, $response);
// 		}
// 		$this->question = preg_replace('/\s/', '', $response);
// 		return $this;
// 	}
//
// 	public function evaluate() {
// 		$this->reponse = $this->parser->e($this->question);
// 		debug_var($this->question);
// 		debug_var($this->reponse);
// 	}
//
// 	public function encode_vars() {
// 		$temp_eq = clone $this;
// 		foreach ($temp_eq->variables as $var => $vals) {
// 			$temp_eq->variables[$var] = $var . '%' . implode('$', $vals);
// 		}
// 		return(implode('!', $temp_eq->variables));
// 	}
//
// 	public function decode_vars($string) {
// 		$varsvals = explode('!', $string);
// 		debug_var($varval);
// 		foreach ($varsvals as $varval) {
// 			$varval = explode('%', $varval);
// 			$vars[] = $varval[0];
// 			$vals[] = $varval[1];
// 			debug_var($vars);
// 			debug_var($vals);
// 		}
// 		$variables = array_combine($vars, $vals);
//
// 		foreach ($variables as $var => $val) {
// 			debug_var($val);
// 			$variables[$var] = explode('$', $val);
// 		}
//
// 		debug_var($variables);
// 	}
//
//
	/**
	 * Getters / Setters
	 */

	public function get_equation()
	{
		return htmlentities($this->equation);
	}

	public function get_variables()
	{
		return $this->variables;
	}

	/**
	 * Utilities
	 */

	public function invert_sign($signe) {
		switch($signe) {
			case '*':
				return "/";
				break;
			case '+':
				return "-";
				break;
			case '-':
				return "+";
				break;
			case '/':
				return "*";
				break;
			default:
				throw new Exception("Unknown mathematical sign !", 1);
		}
	}

	public function is_commutative($signe) {
		$commutative = ['+', '*'];

		return in_array($signe, $commutative);
	}
}
