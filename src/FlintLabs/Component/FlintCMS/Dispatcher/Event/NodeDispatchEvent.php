<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Component\FlintCMS\Dispatcher\Event;
use FlintLabs\Component\FlintCMS\Entity\Node,
Symfony\Component\EventDispatcher\Event;

/**
 * Node event
 * @author camm (camm@flintinteractive.com.au)
 */
class NodeDispatchEvent extends Event
{
    /**
     * The node entity
     * @var \Node
     */
    private $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    /**
     * @return \Node
     */
    public function getNode()
    {
        return $this->node;
    }

    public function __toString()
    {
        return 'NodeDispatchEvent: Node ' . $this->node->getLabel() . ' (ID: ' . $this->node->getId() . ')';
    }

    public function getNodeFragmentType()
    {
        return $this->getNode()->getType();
    }
}
