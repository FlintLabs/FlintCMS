<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintCMS\Bundle\AdminBundle;

/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class FragmentController
{
    /**
     * @Route('/admin/fragment/{fragmentTypeAlias}/create')
     * @param $fragmentTypeAlias
     * @return void
     */
    public function createAction($fragmentTypeAlias)
    {
        // Build the form to modify the specific type

    }
}
