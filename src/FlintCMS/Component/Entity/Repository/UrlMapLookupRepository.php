<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintLabs\Component\FlintCMS\Entity\Repository;
use Doctrine\ORM\EntityRepository;

/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class UrlMapLookupRepository extends EntityRepository
{
    /**
     * @param $url
     * @return void
     */
    public function findOneByUrl($url)
    {
        // TODO: Implement the findOneByUrl() method.
    }

    /**
     * @return void
     */
    public function invalidateAll()
    {
        // TODO: Implement the invalidateAll() method
    }
}
