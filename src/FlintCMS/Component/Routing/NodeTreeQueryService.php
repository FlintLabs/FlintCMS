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
use FlintCMS\Component\Routing\NodeTreeQueryServiceInterface,
Doctrine\ORM\EntityManager;

/**
 * Provides a default implementation for querying the node tree
 * @author camm (camm@flintinteractive.com.au)
 */
class NodeTreeQueryService implements NodeTreeQueryServiceInterface
{
    /**
     * File Reference to the tree
     * @var string
     */
    private $treeFile;

    /**
     * @var array
     */
    private $ignoreTypes = array();

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Instantiates a new NodeTreeQueryService
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param $treeFile
     * @param array $ignoreTypes
     */
    public function __construct(EntityManager $entityManager, $treeFile, $ignoreTypes = array())
    {
        $this->entityManager = $entityManager;
        $this->ignoreTypes = $ignoreTypes;
        $this->treeFile = $treeFile;
    }

    /**
     * Initialises the XML for querying
     * @return void
     */
    private function init()
    {
        if (empty($this->xml)) {
            // TODO: Check if we have yet created a tree file
            $this->xml = new \SimpleXMLElement(file_get_contents($this->treeFile));
        }
    }

    /**
     * Obtains a set of children for the provided node
     * @param $nodeId mixed
     * @return array
     */
    public function getChildren($node)
    {
        $this->init();

        // Get the node ID
        $nodeId = $node;
        if ($node instanceof \FlintCMS\Component\Entity\Node) {
            $nodeId = $node->getId();
        }

        // First locate the item, then grab child instances
        $nodeMatches = $this->xml->xpath("//item[@id='" . $nodeId . "']/item");
        $children = array();
        if (!empty($nodeMatches)) {
            foreach ($nodeMatches as $nodeMatch) {
                $attributes = $nodeMatch->attributes();
                $id = (string)$attributes['id'];
                $label = (string)$attributes['text'];
                $hidden = (string)$attributes['hidden'];

                // Include an ignore types that allows us to strip out navigation options
                if (!empty($this->ignoreTypes)) {
                    $type = (string)$attributes['type'];

                    // If we have don't define an ignore type add it
                    $ignoreThisType = !empty($this->ignoreTypes[$type]);
                    $hidden = ($hidden == 'true');

                    // If we aren't ignoring it
                    if (!$ignoreThisType) {
                        // And it is not hidden
                        if (!$hidden) {
                            // Add it as a child
                            $children[] = array($id => $label);
                        }
                    }
                } else {
                    $children[] = array($id => $label);
                }
            }
        }
        return $children;
    }

    /**
     * Obtains the parent node based on supplied node
     * @param $nodeId mixed
     * @return Node
     */
    public function getParent($node)
    {
        $materialisedPath = $this->getMaterialisedPath($node);
        return current(array_slice($materialisedPath, count($materialisedPath) - 2, 1));
    }

    /**
     * Obtains the materialised path to the node
     * @param $nodeId mixed
     * @return array
     */
    public function getMaterialisedPath($node)
    {
        // Initialise the service
        $this->init();

        // Get the node id
        $nodeId = $node;
        if ($node instanceof \FlintCMS\Component\Entity\Node) {
            $nodeId = $node->getId();
        }

        // Locate the item
        $nodeMatches = $this->xml->xpath("//item[@id='" . $nodeId . "']");
        if (!empty($nodeMatches)) {
            foreach ($nodeMatches as $nodeMatch) {
                $parents = array();
                // Look backwards up the tree at prior item instances
                $parentNodes = $nodeMatch->xpath('ancestor-or-self::item');
                if (!empty($parentNodes)) {
                    foreach ($parentNodes as $parentNode) {
                        // Get the id/text for each item
                        $attributes = $parentNode->attributes();
                        $id = (string)$attributes['id'];
                        $label = (string)$attributes['text'];

                        $parents[] = array($id => $label);
                    }
                }
                return $parents;
            }
        }
        return array();
    }

    /**
     * Determines if the supplied node is hidden by the administrator
     * @param $node
     * @return void
     * @TODO: FREQUENTLY CALLED - MUST OPTIMISE THIS! potentially have 2 versions of flags or 2 tree files?
     */
    public function isHiddenForUser($node)
    {
        //        $repository = $this->entityManager->getRepository('FlintCMS\Component\Entity\Node');
        //        if (!($node instanceof \FlintCMS\Component\Entity\Node)) {
        //            $node = $repository->findOneById($node);
        //        }
        //        $path = $repository->getPath($node);
        //        foreach($path as $element) {
        //            if($element->getFragment()->isHidden()) return true;
        //        }
        return false;
    }
}
