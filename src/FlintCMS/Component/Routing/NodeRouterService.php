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
use FlintCMS\Component\Routing\NodeRouterServiceInterface,
FlintCMS\Component\Entity\UrlMapLookup,
FlintCMS\Component\Entity\Node,
Doctrine\ORM\EntityManager;

/**
 * Identifies the correct routing for given requests to nodes
 * @author camm (camm@flintinteractive.com.au)
 */
class NodeRouterService implements NodeRouterServiceInterface
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $nodeTreeQuery;

    /**
     * @param EntityManager $entityManager
     * @param $treeFile Tree
     */
    public function __construct(NodeTreeQueryServiceInterface $nodeTreeQuery, EntityManager $entityManager)
    {
        $this->nodeTreeQuery = $nodeTreeQuery;
        $this->entityManager = $entityManager;
    }

    /**
     * Identify the nodeId
     * @param $url
     * @return int
     */
    public function findIdByUrl($url)
    {

        if (substr($url, 0, 1) == '/') $url = substr($url, 1);
        $match = $this->entityManager->getRepository('FlintCMS\Component\Entity\UrlMapLookup')->findOneByUrl($url);
        if (!empty($match))
            return $match->getNode()->getId();
    }

    /**
     * Identify the URL by the supplied node id
     * @return void
     */
    public function findURLById($nodeId)
    {

        if ($nodeId instanceof FlintCMS\Component\Entity\Node) {
            $nodeId = $nodeId->getId();
        }

        // Construct if we can find quickly
        $path = $this->nodeTreeQuery->getMaterialisedPath($nodeId);
        if (!empty($path)) {
            return $this->generateUrl($path);
        }

        // Revert back to url map
        $match = $this->entityManager->getRepository('FlintCMS\Component\Entity\UrlMapLookup')->findOneByNode($nodeId);
        if (!empty($match))
            return $match->getUrl();
    }

    /**
     * Generates our path based on the materialised path to the element
     * @param $materialisedPath
     * @param bool $includeRoot
     * @return mixed|string
     */
    public function generateUrl($materialisedPath, $includeRoot = false)
    {
        // If we don't want to include the root element in the URL
        if ($includeRoot == false) {
            $materialisedPath = array_slice($materialisedPath, 1);
        }

        // Create the URL on a simple implode/replace strategy
        if (empty($materialisedPath)) return '';
        if (is_array($materialisedPath[0])) {
            $newMaterialisedPath = array();
            foreach ($materialisedPath as $entry) $newMaterialisedPath[] = current($entry);
            $materialisedPath = $newMaterialisedPath;
        }

        // Create the URL based on stripping non whitespace/digits
        $url = implode('/', $materialisedPath);
        $url = preg_replace('/[^\/\s\w\d]/', '', $url);
        $url = trim($url); // Remove any trailing spaces
        $url = strtolower(preg_replace('/[\s]/', '-', $url));
        $url = preg_replace('/[\-]{2}/', '-', $url);

        return $url;
    }
}
