<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Bundle\FlintCMSBundle\EventListener\Fragment;
use FlintLabs\Component\FlintCMS\Dispatcher\FragmentDispatchListenerInterface,
FlintLabs\Component\FlintCMS\Dispatcher\Event\FragmentDispatchEvent,
FlintLabs\Bundle\FlintCMSBundle\Exception\ResponseException,
FlintLabs\Component\FlintCMS\Routing\NodeRouterServiceInterface,
Symfony\Component\HttpFoundation\RedirectResponse,
Symfony\Component\HttpKernel\Log\LoggerInterface,
Symfony\Component\HttpFoundation\Response;
/**
 * Handles a dispatch event for a redirect node
 * @author camm camm@flintinteractive.com.au
 */
class RedirectFragmentListener implements FragmentDispatchListenerInterface
{

    private $router;
    private $log;

    public function __construct(NodeRouterServiceInterface $router, LoggerInterface $log = null)
    {
        $this->router = $router;
        $this->log = $log;
    }

    /**
     * Handle encountering a redirect fragment
     * @param FragmentDispatchEvent $fragmentDispatchEvent
     * @return void
     */
    public function onFragmentDispatch(FragmentDispatchEvent $fragmentDispatchEvent)
    {
        // Check for our redirect type
        if ($fragmentDispatchEvent->getFragmentType() !== 'redirect') {
            return;
        }

        // Get the data
        $data = $fragmentDispatchEvent->getFragment()->getData();

        // Obtain the link type match
        $xml = new \SimpleXMLElement($data);
        $linkTypeMatches  = $xml->xpath('/data/link/type');
        if (empty($linkTypeMatches)) return;
        $linkType = (string)current($linkTypeMatches);

        switch ($linkType) {
            case 'url':
                $url = (string)current($xml->xpath('/data/link/url'));
                break;
                throw new ResponseException(new RedirectResponse($sxml));
            case 'file':
                $url = (string)current($xml->xpath('/data/link/file'));
                break;
                throw new ResponseException(new RedirectResponse($sxml));
            case 'node':
                $nodeId = (string)current($xml->xpath('/data/link/node'));
                $url = $this->router->findURLById($nodeId);
                if (($url)) {
                    // Can't locate this URL
                    throw new ResponseException(new Response(404));
                }
                break;
        }
        throw new ResponseException(new RedirectResponse($url));

    }
}
