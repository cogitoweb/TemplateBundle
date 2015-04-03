<?php

namespace Cogitoweb\TemplateBundle\Query\Postgresql;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class Any extends FunctionNode {

    public $field;


    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
        return 'ANY(' .
            $this->field->dispatch($sqlWalker) . ')';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->field = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}