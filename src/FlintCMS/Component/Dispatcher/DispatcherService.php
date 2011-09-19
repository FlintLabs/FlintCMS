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
use FlintCMS\Component\Routing\NodeRouterServiceInterface,
FlintCMS\Component\Dispatcher\Event\FragmentDispatchEvent,
FlintCMS\Component\Dispatcher\Event\NodeDispatchEvent,
FlintCMS\Bundle\FrontBundle\Exception\ResponseException,
Symfony\Component\EventDispatcher\EventDispatcherInterface,
Symfony\Component\HttpKernel\Exception\HttpException,
Symfony\Component\HttpKernel\Log\LoggerInterface,
Doctrine\ORM\EntityManager;

/**
 * Dispatches the necessary actions for a URL (attached to a node)
 * @author camm (camm@flintinteractive.com.au)
 */
class DispatcherService implements DispatcherServiceInterface
{

    /**
     * @var \EntityManager
     */
    private $entityManager;

    /**
     * @var \EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    private $log;

    /**
     * @var FlintCMS\Component\Entity\Node
     */
    private $node;

    /**
     * @param EntityManager $entityManager
     * @param EventDispatcher $eventDispatcher
     * @param RouterService $router
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $eventDispatcher, LoggerInterface $log = null)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->log = $log;
    }

    /**
     * Create a response or provide data for our view
     * @param $url
     * @return array|HTTPResponse
     * @TODO: Consider a dispatch based on 'request' not URL (obtain from request - so controllers can forward easier?)
     */
    public function dispatch($nodeId)
    {
        try {
            if (empty($nodeId)) throw new HttpException(404);

            // Get the entity manager for FragmentData
            $this->node = $this->entityManager->getRepository('FlintCMS\Component\Entity\Node')->findOneById($nodeId);
            if (empty($this->node)) throw new HttpException(404);

            // Create the dispatch event for encountering a node (allowing behaviour to be invoked for specific nodes)
            $this->eventDispatcher->dispatch('nodeDispatch', new NodeDispatchEvent($this->node));

            // Create the dispatch event for encountering a fragment
            $this->eventDispatcher->dispatch('fragmentDispatch', new FragmentDispatchEvent($fragment = $this->node->getFragment()));

            // Obtain the children
            $fragmentRepository = $this->entityManager->getRepository('FlintCMS\Component\Entity\Fragment');
            $fragmentRepository->loadRegionFragments($fragment);

            // For any children fragment, create dispatch events for those fragments
            $children = $fragment->getContainedChildrenRegionFragments();
            if (!empty($children)) {
                foreach ($children as $region => $elements) {
                    foreach ($elements as $sortOrder => $childFragment) {
                        $this->eventDispatcher->dispatch('fragmentDispatch', new FragmentDispatchEvent($childFragment));
                    }
                }
            }


        } catch (\Exception $e) {
            // Log an exception for more detailed tracing
            if (!empty($this->log)) {
                $this->log->debug('Encountered exception ' . get_class($e) . ' for Node ID: ' . $nodeId . (!empty($this->node)
                                          ? $this->node : ''));
            }
            throw $e;
        }

        // Dispatch will return an array reference to the dispatched node/fragment entities
        return array_merge(array('node' => $this->node), $fragment->getViewData());
    }

    /**
     * @return void
     */
    public function getNode()
    {
        return $this->node;
    }


}