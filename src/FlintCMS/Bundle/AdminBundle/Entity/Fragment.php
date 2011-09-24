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
 * @ORM\Entity(repositoryClass="FlintCMS\Bundle\AdminBundle\Entity\Repository\FragmentRepository")
 * @ORM\Table(name="fragment")
 * @author camm (camm@flintinteractive.com.au)
 */
class Fragment implements ViewModelContainerInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column
     * @var string
     */
    protected $type;

    /**
     * @ORM\ORM:Column(type='object')
     * @var string
     */
    protected $data;

    /**
     * @var array
     */
    protected $viewData;

    /**
     * @ORM\OneToMany(targetEntity="Fragment", mappedBy="parent")
     * @var ArrayCollection
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="Fragment", inversedBy="children")
     * @var Fragment
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer", name="region", nullable="true")
     * @var int
     */
    protected $region;

    /**
     * @ORM\Column(type="integer", name="sort_order", nullable="true")
     * @var int
     */
    protected $sortOrder;

    /**
     * @ORM\Column(name="created", type="datetime")
     * @var \DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="integer", name="version")
     * @var int
     */
    protected $version;

    
    protected $user;

    /**
     * @return void
     */
    public function getContainedChildrenRegionFragments()
    {
        // TODO: Implement the getContainedChildrenRegionFragments() method
    }

    /**
     * Sets the view data array for this fragment
     * @param $array
     * @return void
     */
    public function setViewData($array)
    {
        // TODO: Implement setViewData() method.
    }

    /**
     * Retrieves the current view data
     * @return void
     */
    public function getViewData()
    {
        // TODO: Implement getViewData() method.
    }

    /**
     * Adds view data encapsulated with context to this fragment
     * @param $key
     * @param $value
     * @return void
     */
    public function addViewData($key, $value)
    {
        // TODO: Implement addViewData() method.
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }


}


 
