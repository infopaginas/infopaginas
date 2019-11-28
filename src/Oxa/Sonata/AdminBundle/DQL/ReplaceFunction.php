<?php

namespace Oxa\Sonata\AdminBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class ReplaceFunction extends FunctionNode
{
    public $stringFirst;
    public $stringSecond;
    public $stringThird;

    /**
     * @param $sqlWalker \Doctrine\ORM\Query\SqlWalker
     *
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf(
            'REGEXP_REPLACE(%s,%s,%s)',
            $this->stringFirst->dispatch($sqlWalker),
            $this->stringSecond->dispatch($sqlWalker),
            $this->stringThird->dispatch($sqlWalker)
        );
    }

    /**
     * @param $parser \Doctrine\ORM\Query\Parser
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->stringFirst = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->stringSecond = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->stringThird = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
