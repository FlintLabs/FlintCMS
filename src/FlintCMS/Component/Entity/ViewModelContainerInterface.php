<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintLabs\Component\FlintCMS\Entity;

/**
 * Fragments have extended and dynamic properties for the view layer
 * @author camm (camm@flintinteractive.com.au)
 */
interface ViewModelContainerInterface
{
    /**
     * Sets the view data array for this fragment
     * @abstract
     * @param $array
     * @return void
     */
    public function setViewData($array);

    /**
     * Retrieves the current view data
     * @abstract
     * @return void
     */
    public function getViewData();

    /**
     * Adds view data encapsulated with context to this fragment
     * @abstract
     * @param $key
     * @param $value
     * @return void
     */
    public function addViewData($key, $value);
}
