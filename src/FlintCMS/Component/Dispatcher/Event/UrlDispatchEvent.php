<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Component\Dispatcher\Event;
use Symfony\Component\HttpFoundation\Response,
Symfony\Component\EventDispatcher\Event;
/**
 * Dispatch event that can customise the URL before being looked up or handle returning a response
 * @author camm (camm@flintinteractive.com.au)
 */
class UrlDispatchEvent extends Event
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param  $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return
     */
    public function getResponse()
    {
        return $this->response;
    }
}
