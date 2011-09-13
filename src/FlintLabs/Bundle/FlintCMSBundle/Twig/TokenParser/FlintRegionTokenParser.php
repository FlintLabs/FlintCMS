<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FlintCMSBundle\Twig\TokenParser;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class FlintRegionTokenParser extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    function parse(\Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $variables = null;
        if ($this->parser->getStream()->test(\Twig_Token::NAME_TYPE, 'with')) {
            $this->parser->getStream()->next();

            $variables = $this->parser->getExpressionParser()->parseExpression();
        }

        $only = false;
        if ($this->parser->getStream()->test(\Twig_Token::NAME_TYPE, 'only')) {
            $this->parser->getStream()->next();

            $only = true;
        }

        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        return new \FlintLabs\Bundle\FlintCMSBundle\Twig\Node\PureRegionNode($expr, $variables, $only, $token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    function getTag()
    {
        return 'flint_region';
    }

}
