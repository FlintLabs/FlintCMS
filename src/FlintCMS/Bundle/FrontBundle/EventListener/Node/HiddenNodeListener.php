<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FlintCMSBundle\EventListener\Node;
use FlintLabs\Component\FlintCMS\Dispatcher\NodeDispatchListenerInterface,
FlintLabs\Component\FlintCMS\Dispatcher\Event\NodeDispatchEvent,
FlintLabs\Bundle\FlintCMSBundle\Exception\ResponseException,
Symfony\Component\HttpFoundation\Response,
FlintLabs\Component\FlintCMS\Routing\NodeTreeQueryService;

/**
 * Handles a dispatch event with a hidden node
 * @author camm camm@flintinteractive.com.au
 */
class HiddenNodeListener implements NodeDispatchListenerInterface
{
    private $nodeTreeQuery;

    public function __construct(NodeTreeQueryService $nodeTreeQuery)
    {
        $this->nodeTreeQuery = $nodeTreeQuery;
    }

    /**
     * Handles the event of encountering a node
     * @param NodeDispatchEvent $nodeDispatchEvent
     * @return void
     */
    public function onNodeDispatch(NodeDispatchEvent $nodeDispatchEvent)
    {
        // Quick match
        if($this->nodeTreeQuery->isHiddenForUser($nodeDispatchEvent->getNode())){
            throw new ResponseException(new Response(404));
        }
    }

}
