<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintLabs\Bundle\FlintCMSBundle\Twig;
/**
 * A Helper interface for working with URL and Node Tree Navigation
 * @author camm (camm@flintinteractive.com.au)
 */
interface NavigationHelperInterface {

    /**
     * Obtains the URL for the current node
     * @abstract
     * @param $node
     * @return void
     */
    public function getUrl($node);

    /**
     * Obtains whether this node is on the active path
     * @abstract
     * @param $node
     * @return void
     */
    public function isActive($node);

    /**
     * Obtains the children of the current node
     * @abstract
     * @param $node
     * @return void
     */
    public function getChildren($node);

    /**
     * Obtains the parent of the supplied node
     * @abstract
     * @param $node
     * @return void
     */
    public function getParent($node);

    /**
     * Obtains the path to the provided node
     * @abstract
     * @param $node
     * @return void
     */
    public function getPath($node);

    /**
     * Return the top level children
     * @param $node
     * @abstract
     * @return void
     */
    public function top($node);

}
