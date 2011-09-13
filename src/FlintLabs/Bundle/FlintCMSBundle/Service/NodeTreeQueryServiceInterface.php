<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FlintCMSBundle\Service;

/**
 * Used for managing node tree querying
 * @author camm (camm@flintinteractive.com.au)
 */
interface NodeTreeQueryServiceInterface
{
    /**
     * Obtains a set of children for the provided node
     * @abstract
     * @param $node mixed
     * @return array
     */
    public function getChildren($node);

    /**
     * Obtains the parent node based on supplied node
     * @abstract
     * @param $node mixed
     * @return Node
     */
    public function getParent($node);

    /**
     * Obtains the materialised path to the node
     * @abstract
     * @param $node mixed
     * @return array
     */
    public function getMaterialisedPath($node);

    /**
     * Determines if the supplied node is hidden by the administrator
     * @abstract
     * @param $node
     * @return void
     */
    public function isHiddenForUser($node);

}
