<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Component\Dispatcher;
use FlintCMS\Component\Dispatcher\Event\NodeDispatchEvent;

/**
 * On encountering nodes
 * @author camm (camm@flintinteractive.com.au)
 */
interface NodeDispatchListenerInterface
{
    /**
     * Handle encountering a node
     * @abstract
     * @param NodeDispatchEvent $nodeDispatchEvent
     * @return void
     */
    public function onNodeDispatch(NodeDispatchEvent $nodeDispatchEvent);
}
