<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintCMS\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="FlintLabs\Bundle\FlintCMSBundle\Entity\Repository\UrlMapLookupRepository")
 * @ORM\Table(name="url_map_lookup")
 * @author camm (camm@flintinteractive.com.au)
 */
class UrlMaplookup
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="urlMapLookups", fetch="EAGER")
     * @ORM\var Node
     */
    protected $node;

    /**
     * @ORM\Column
     * @ORM\var string
     */
    protected $url;

    /**
     * @ORM\Column(type="integer")
     * @ORM\var int
     */
    protected $state;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\var \DateTime
     */
    protected $created;


    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setNode($node)
    {
        $this->node = $node;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
