<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintCMS\Bundle\FrontProfilerBundle\DataCollector;

/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
interface DispatcherServiceDataCollectorInterface
{
    /**
     * @return void
     */
    public function getNode();
}
