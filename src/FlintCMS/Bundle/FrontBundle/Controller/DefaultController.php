<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Bundle\FrontBundle\Controller;
use FlintCMS\Component\Dispatcher\DispatcherServiceInterface,
FlintCMS\Component\Template\TemplateMappingServiceInterface,
FlintCMS\Component\Routing\NodeRouterServiceInterface,
FlintCMS\Bundle\FrontBundle\Exception\ResponseException,
FlintCMS\Component\Dispatcher\Event\UrlDispatchEvent,
Symfony\Component\HttpKernel\Exception\HttpException,
Symfony\Component\HttpKernel\Log\LoggerInterface,
Symfony\Component\Templating\EngineInterface,
Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The default controller for the frontend
 * @author camm (camm@flintinteractive.com.au)
 * @TODO: Allow for alternate rendering of formats (xml, json etc)
 */
class DefaultController
{
    /**
     * The router used for URL routing to nodes
     * @var NodeRouterServiceInterface
     */
    private $router;

    /**
     * Dispatcher used for dispatching the node events
     * @var DispatcherServiceInterface
     */
    private $dispatcher;

    /**
     * Templating mapper for mapping nodes to correct views
     * @var \FlintCMS\Component\Template\TemplateMappingServiceInterface
     */
    private $templateMapping;

    /**
     * Template engine for handling rendering
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $templateEngine;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface
     */
    private $log;

    /**
     * @param \FlintCMS\Component\Routing\NodeRouterServiceInterface $router
     * @param \FlintCMS\Component\Dispatcher\DispatcherServiceInterface $dispatcher
     * @param \FlintCMS\Component\Template\TemplateMappingServiceInterface $templateMapping
     * @param \Symfony\Component\Templating\EngineInterface $templateEngine
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \Symfony\Component\HttpKernel\Log\LoggerInterface $log
     */
    public function __construct(NodeRouterServiceInterface $router,
        DispatcherServiceInterface $dispatcher,
        TemplateMappingServiceInterface $templateMapping,
        EngineInterface $templateEngine,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $log)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->templateMapping = $templateMapping;
        $this->templateEngine = $templateEngine;
        $this->eventDispatcher = $eventDispatcher;
        $this->log = $log;
    }

    /**
     * Uses the router to lookup a URL to find a node ID to dispatch
     * @throws \Exception
     * @return
     */
    public function indexAction($url)
    {
        try {
            // Add a event dispatcher "handle URL" that could allow listeners to modify
            $urlDispatchEvent = new UrlDispatchEvent($url);
            $this->eventDispatcher->dispatch('urlDispatch', $urlDispatchEvent);
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        // Look for a response in the event
        $response = $urlDispatchEvent->getResponse();
        if (!empty($response)) {
            return $response;
        } else {
            // If no listener has provided a response, forward
            return $this->nodeAction($this->router->findIdByUrl($urlDispatchEvent->getUrl()));
        }
    }

    /**
     * Dispatches a matching node ID
     * @throws \Exception
     * @param $nodeId
     * @return
     */
    public function nodeAction($nodeId)
    {
        try {
            // Identify the URL and dispatch the page
            $response = $this->dispatcher->dispatch($nodeId);
            if (is_array($response)) {
                if (empty($response['node'])) {
                    return new HttpException(501, 'Unable to process node request');
                }

                // If we have a response to serve, map to our template
                $template = $this->templateMapping->getTemplateForFragment($response['node']->getFragment());
                return $this->renderResponse($template, $response);
            } else return $response;
        } catch (ResponseException $e) {
            return $e->getResponse();
        }
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @param $template
     * @param $response
     * @return
     */
    protected function renderResponse($template, $response)
    {
        if (is_array($template)) {
            // Multiple locations of possible templates configured
            $loaded = false;
            foreach ($template as $templateFile) {
                if ($this->templateEngine->exists($templateFile)) {
                    return $this->templateEngine->renderResponse($templateFile, $response);
                }
            }
            if (!$loaded) {
                $this->log->err('Unable to locate the template for this node trying at ' . implode(', ', $template));
                throw new HttpException(404, 'Template not found');
            }
        }
        if (!$this->templateEngine->exists($template)) {
            $this->log->err('Unable to locate the template for this node trying at ' . $template);
            throw new HttpException(404, 'Template not found');
        }
        return $this->templateEngine->renderResponse($template, $response);
    }
}
