<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Bundle\FrontBundle\Exception;
use FlintCMS\Bundle\FrontBundle\Exception\ResponseException,
Symfony\Component\HttpFoundation\Response;

/**
 * Encapsulate a response event for immediate interruption to processing
 * @author camm (camm@flintinteractive.com.au)
 */
class ResponseException extends \Exception
{
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;

    /**
     * Instantiate a new response exception
     * @param $response \Symfony\Component\HttpFoundation\Response
     */
    public function __construct(Response $response, $message = null)
    {
        parent::__construct($message);
        $this->response = $response;
    }

    /**
     * Access the HTTP Response to serve
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
