<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FlintCMS\Component\Dispatcher;
use FlintCMS\Component\Dispatcher\Event\FragmentDispatchEvent;

/**
 * Listens to events
 * @author camm (camm@flintinteractive.com.au)
 */
interface FragmentDispatchListenerInterface
{
    /**
     * Handles the event of encountering a fragment
     * @abstract
     * @param FragmentDispatchEvent $fragmentDispatchEvent
     * @return void
     */
    public function onFragmentDispatch(FragmentDispatchEvent $fragmentDispatchEvent);
}
