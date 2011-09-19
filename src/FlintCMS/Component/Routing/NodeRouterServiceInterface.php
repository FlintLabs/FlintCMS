<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Component\Routing;
/**
 * An interface for identifing the correct page from the URL
 * @author camm (camm@flintinteractive.com.au)
 */
interface NodeRouterServiceInterface
{
    /**
     * Identify the nodeId
     * @abstract
     * @param $url
     * @return int
     */
    public function findIdByUrl($url);

    /**
     * Identify the URL by the supplied node id
     * @abstract
     * @param $node int
     * @return void
     */
    public function findURLById($node);
}
