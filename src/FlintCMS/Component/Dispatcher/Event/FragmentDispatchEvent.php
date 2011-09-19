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
use FlintCMS\Component\Entity\Fragment,
Symfony\Component\EventDispatcher\Event;

/**
 * On fragment event
 * @author camm (camm@flintinteractive.com.au)
 */
class FragmentDispatchEvent extends Event
{
    /**
     * $the fragment entity
     * @var \FlintCMS\Component\Entity\Fragment
     */
    private $fragment;

    /**
     * @param \FlintCMS\Component\Entity\Fragment $fragment
     */
    public function __construct(Fragment $fragment)
    {
        $this->fragment = $fragment;
    }

    /**
     * @return \FlintCMS\Component\Entity\Fragment
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    public function __toString()
    {
        return 'NodeDispatchEvent: Fragment (ID: ' . $this->fragment->getId() . ')';
    }

    public function getFragmentType()
    {
        return $this->fragment->getType();
    }
}
