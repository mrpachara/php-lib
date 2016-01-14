<?php
namespace sys;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\Node;

use PhpParser\Node\Scalar;
use PhpParser\Node\Name;

use PhpParser\Node\Expr\ConstFetch;

use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\ArrayDimFetch;

use PhpParser\Node\Expr\BooleanNot;

use PhpParser\Node\Expr\BinaryOp\Plus;
use PhpParser\Node\Expr\BinaryOp\Minus;
use PhpParser\Node\Expr\BinaryOp\Mul;
use PhpParser\Node\Expr\BinaryOp\Div;
use PhpParser\Node\Expr\BinaryOp\Mod;
use PhpParser\Node\Expr\BinaryOp\Concat;

use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BinaryOp\Smaller;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\BinaryOp\Greater;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;

use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;

use PhpParser\Node\Expr\Ternary;

use PhpParser\Node\Expr\FuncCall;

/*
 * Safe ways to execute PHP expression
 */
class PHPExpressionExecutor{
	protected $model;
	protected $funcs;

	protected $parser;

	function __construct($model, $funcs = null){
		$this->model = $model;
		$this->funcs = $funcs;

		$this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
	}

	protected function getFunction(Name $name){
		$func = $this->funcs;
		foreach($name->parts as $part){
			if(!is_array($func) || !array_key_exists($part, $func)) return null;
			$func = $func[$part];
		}

		return $func;
	}

	protected function getExcutedValue(Node $node){
		/* scalar */
		if($node instanceof Scalar){
			return $node->value;

		/* Constant */
		}else if($node instanceof ConstFetch){
			if($node->name->parts[0] === 'true'){
				return true;
			} else if($node->name->parts[0] === 'false'){
				return false;
			} else{
				return null;
			}

		/* Variable */
		} else if($node instanceof Variable){
			if(array_key_exists($node->name, $this->model)){
				return $this->model[$node->name];
			} else{
				return null;
			}
		} else if($node instanceof ArrayDimFetch) {
			$var = $this->getExcutedValue($node->var);
			$dim = $this->getExcutedValue($node->dim);
			if(is_array($var) && ($dim !== null) && (array_key_exists($dim, $var))){
				return $var[$dim];
			} else{
				return null;
			}

		/* Unary */
		} else if($node instanceof BooleanNot){
			return !($this->getExcutedValue($node->expr));

		/* Operator */
		} else if($node instanceof Plus){
			return $this->getExcutedValue($node->left) + $this->getExcutedValue($node->right);
		} else if($node instanceof Minus){
			return $this->getExcutedValue($node->left) - $this->getExcutedValue($node->right);
		} else if($node instanceof Mul){
			return $this->getExcutedValue($node->left) * $this->getExcutedValue($node->right);
		} else if($node instanceof Div){
			return $this->getExcutedValue($node->left) / $this->getExcutedValue($node->right);
		} else if($node instanceof Mod){
			return $this->getExcutedValue($node->left) % $this->getExcutedValue($node->right);
		} else if($node instanceof Concat){
			return $this->getExcutedValue($node->left) . $this->getExcutedValue($node->right);

		} else if($node instanceof Equal){
			return $this->getExcutedValue($node->left) == $this->getExcutedValue($node->right);
		} else if($node instanceof Identical){
			return $this->getExcutedValue($node->left) === $this->getExcutedValue($node->right);
		} else if($node instanceof NotEqual){
			return $this->getExcutedValue($node->left) != $this->getExcutedValue($node->right);
		} else if($node instanceof NotIdentical){
			return $this->getExcutedValue($node->left) !== $this->getExcutedValue($node->right);
		} else if($node instanceof Smaller){
			return $this->getExcutedValue($node->left) < $this->getExcutedValue($node->right);
		} else if($node instanceof SmallerOrEqual){
			return $this->getExcutedValue($node->left) <= $this->getExcutedValue($node->right);
		} else if($node instanceof Greater){
			return $this->getExcutedValue($node->left) > $this->getExcutedValue($node->right);
		} else if($node instanceof GreaterOrEqual){
			return $this->getExcutedValue($node->left) >= $this->getExcutedValue($node->right);

		} else if($node instanceof BooleanAnd){
			return $this->getExcutedValue($node->left) && $this->getExcutedValue($node->right);
		} else if($node instanceof BooleanOr){
			return $this->getExcutedValue($node->left) || $this->getExcutedValue($node->right);

		/* Ternary */
		} else if($node instanceof Ternary){
			$cond = $this->getExcutedValue($node->cond);
			if($cond){
				return $this->getExcutedValue($node->if);
			} else{
				return $this->getExcutedValue($node->else);
			}

		/* function call */
		} else if($node instanceof FuncCall){
			$func = $this->getFunction($node->name);
			if(empty($func)) return null;
			$args = [];

			foreach($node->args as $arg) $args[] = $this->getExcutedValue($arg->value);

			return call_user_func_array($func, $args);
		} else{
			return null;
		}
	}

	public function getValue($expr){
		$nodes = $this->parser->parse('<?php '.$expr.';');
		return $this->getExcutedValue($nodes[0]);
	}
}
?>
