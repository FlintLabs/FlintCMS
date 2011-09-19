<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintLabs\Component\FlintCMS\Dispatcher;
/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
interface UrlDispatchListenerInterface
{

    /**
     * Listen to the urlDispatch event and craft a response.
     * @abstract
     * @param UrlDispatchEvent $urlEvent
     * @return void
     */
    function onUrlDispatch(UrlDispatchEvent $urlEvent);
}
