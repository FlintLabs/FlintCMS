<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Bundle\FrontBundle\Twig\Node;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class FlintRegionNode extends \Twig_Node implements \Twig_NodeOutputInterface
{
    /**
     * @param Twig_Node_Expression $expr
     * @param Twig_Node_Expression|null $variables
     * @param bool $only
     * @param $lineno
     * @param null $tag
     */
    public function __construct(\Twig_Node_Expression $expr, \Twig_Node_Expression $variables = null, $only = false, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr, 'variables' => $variables), array('only' => (Boolean) $only), $lineno, $tag);
    }

    /**
     * @param Twig_Compiler $compiler
     * @return void
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write("\$region = ")
            ->subcompile($this->getNode('expr'))
            ->raw(";\n")
            ->write("echo \$this->env->getExtension('pure')->renderRegion(")
            ->raw('$this->env, $region, ')
        ;

        if (false === $this->getAttribute('only')) {
            if (null === $this->getNode('variables')) {
                $compiler->raw('$context');
            } else {
                $compiler
                    ->raw('array_merge($context, ')
                    ->subcompile($this->getNode('variables'))
                    ->raw(')')
                ;
            }
        } else {
            if (null === $this->getNode('variables')) {
                $compiler->raw('array()');
            } else {
                $compiler->subcompile($this->getNode('variables'));
            }
        }

        $compiler->raw(");\n");
    }
}
