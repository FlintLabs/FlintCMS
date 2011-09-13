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
 * The dispatcher service handles dispatching encountered nodes
 * @author camm (camm@flintinteractive.com.au)
 */
interface DispatcherServiceInterface
{

    /**
     * Create a response or provide data for our view
     * @abstract
     * @param $nodeId
     * @return array|HTTPResponse
     */
    public function dispatch($nodeId);
}
