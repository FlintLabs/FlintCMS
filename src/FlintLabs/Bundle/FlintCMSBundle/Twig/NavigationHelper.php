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
use FlintLabs\Bundle\FlintCMSBundle\Service\NodeTreeQueryServiceInterface,
FlintLabs\Component\FlintCMS\Routing\NodeRouterServiceInterface;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class NavigationHelper implements NavigationHelperInterface
{
    /**
     * @var \FlintLabs\Component\FlintCMS\Routing\NodeRouterServiceInterface
     */
    private $nodeRouter;
    
    /**
     * @var \FlintLabs\Bundle\FlintCMSBundle\Service\NodeTreeQueryServiceInterface
     */
    private $nodeTreeQuery;

    /**
     * @param \FlintLabs\Component\FlintCMS\Routing\NodeRouterServiceInterface $nodeRouter
     * @param \FlintLabs\Bundle\FlintCMSBundle\Service\NodeTreeQueryServiceInterface $nodeTreeQuery
     */
    public function __construct(NodeRouterServiceInterface $nodeRouter, NodeTreeQueryServiceInterface $nodeTreeQuery)
    {
        $this->nodeRouter = $nodeRouter;
        $this->nodeTreeQuery = $nodeTreeQuery;
    }

    /**
     * Obtains the URL for the current node
     * @param $node
     * @return void
     */
    public function getUrl($node)
    {
        return $this->nodeRouter->findURLById($this->getNodeId($node));
    }

    /**
     * Obtains whether this node is on the active path
     * @param $node
     * @return void
     */
    public function isActive($node)
    {
        // Obtain the node ID and active path
        $nodeId = $this->getNodeId($node);
        $path = $this->nodeTreeQuery->getMaterialisedPath($nodeId);

        // Compare on the path for the nodeId
        foreach($path as $item) {
            if($nodeId == key($item)) {
                return true; // Found on path
            }
        }
        return false; // Not found on path
    }

    /**
     * Obtains the children of the current node
     * @param $node
     * @return void
     */
    public function getChildren($node, $depth = null)
    {
        if(!empty($depth)) {
            $path = $this->getPath($this->getNodeId($node));
            if($depth > count($path)) return array();
            $level = array_slice($path, $depth-1, 1);
            $node = $this->getNodeId(current($level));
        }

        $returnVal = array();
        $children = $this->nodeTreeQuery->getChildren($this->getNodeId($node));
        if(!empty($children)) foreach($children as $element) {
            $returnVal[] = array('id' => key($element), 'label' => current($element));
        }
        return $returnVal;
    }

    /**
     * Obtains the parent of the supplied node
     * @param $node
     * @return void
     */
    public function getParent($node)
    {
        $parent = $this->nodeTreeQuery->getParent($this->getNodeId($node));
        if(empty($parent)) return; // no parent?

        // Compose the array in a more meaningful frontend manor
        $returnVal = array();
        $returnVal['label'] = current($parent);
        $returnVal['id'] = key($current);
        return $returnVal;
    }

    /**
     * Obtains the path to the provided node
     * @param $node
     * @return void
     */
    public function getPath($node)
    {
        $returnVal = array();
        $path = $this->nodeTreeQuery->getMaterialisedPath($this->getNodeId($node));
        if(!empty($path)) foreach($path as $element) {
            $returnVal[] = array('id' => key($element), 'label' => current($element));
        }
        return $returnVal;
    }

    /**
     * Identify the node ID from the expression
     * @param $node
     * @return
     */
    private function getNodeId($node)
    {
        $nodeId = $node;
        if($node instanceof \FlintLabs\Component\FlintCMS\Entity\Node) {
            $nodeId = $node->getId();
        } else if(is_array($node) && !empty($node['id'])) {
            $nodeId = $node['id'];
        } else if(is_string($node)) {
            try {
                $nodeId = $this->nodeRouter->findIdByUrl($node);
            } catch(\Exception $e) {}
        }
        return $nodeId;
    }
    
    /**
     * Return the top level children
     * @return void
     */
    public function top($node)
    {
        return $this->getChildren($node, 1); // 1 is first set of children
    }
}
