<?php

namespace Cogitoweb\TemplateBundle\Query\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Any extends FunctionNode
{
	/**
	 *
	 * @var string
	 */
	public $field;

	/**
	 * {@inheritdoc}
	 */
	public function getSql(SqlWalker $sqlWalker)
	{
		return sprintf('ANY(%s)', $this->field->dispatch($sqlWalker));
	}

	/**
	 * {@inheritdoc}
	 */
	public function parse(Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);

		$this->field = $parser->StringPrimary();

		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}
}