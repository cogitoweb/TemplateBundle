<?php

namespace Cogitoweb\TemplateBundle\Query\Postgresql;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Overlap extends FunctionNode {

    protected $first;
    protected $second;

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
        return sprintf(
			"%s && %s",
			$this->first->dispatch($sqlWalker),
			$this->second->dispatch($sqlWalker)
		);
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->first = $parser->StringPrimary();
		$parser->match(Lexer::T_COMMA);
		$this->second = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}