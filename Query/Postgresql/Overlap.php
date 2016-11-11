<?php

namespace Cogitoweb\TemplateBundle\Query\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

class Overlap extends FunctionNode
{
	/**
	 *
	 * @var string
	 */
    protected $first, $second;

	/**
	 * {@inheritdoc}
	 */
    public function getSql(SqlWalker $sqlWalker)
	{
        return sprintf('%s && %s', $this->first ->dispatch($sqlWalker), $this->second->dispatch($sqlWalker));
    }

	/**
	 * {@inheritdoc}
	 */
    public function parse(Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);

		$this->first = $parser->StringPrimary();

		$parser->match(Lexer::T_COMMA);

		$this->second = $parser->StringPrimary();

		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}