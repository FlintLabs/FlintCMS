<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintCMS\Bundle\AdminBundle\EventListener;

use FlintCMS\Component\Dispatcher\NodeDispatchListenerInterface;
use FlintCMS\Component\Dispatcher\Event\NodeDispatchEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Implements a web profiler type panel for the admin controls
 * @author camm (camm@flintinteractive.com.au)
 */
class AdminToolbarListener implements NodeDispatchListenerInterface
{

    private $node;
    private $templating;

    public function __construct(TwigEngine $templating)
    {
        $this->templating = $templating;
    }


    public function isEnabled()
    {
        // TODO: Update with the site role security
        return true;

    }

    /**
     * @param FilterResponseEvent $event
     * @return
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if(!$this->isEnabled()) return;

        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $this->injectToolbar($response);
    }

    /**
     * Handle encountering a node
     * @param NodeDispatchEvent $nodeDispatchEvent
     * @return void
     */
    public function onNodeDispatch(NodeDispatchEvent $nodeDispatchEvent)
    {
        if(!$this->isEnabled()) return;
        $this->node = $nodeDispatchEvent->getNode();
    }


    public function injectToolbar($response)
    {
        // Insert into the toolbar
        if (function_exists('mb_stripos')) {
            $posrFunction = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();

        if (false !== $pos = $posrFunction($content, '</body>')) {
            $toolbar = "\n".str_replace("\n", '', $this->templating->render(
                'FlintCMSAdminBundle:Panel:toolbar.html.twig',
                array('node' => $this->node)
            ))."\n";
            $content = $substrFunction($content, 0, $pos).$toolbar.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }
}
