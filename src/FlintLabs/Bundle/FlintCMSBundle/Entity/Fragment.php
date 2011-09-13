<?php
/*
 * This file is part of the Flint CMS package
 *
 * (c) Cameron Manderson <camm@flintinteractive.com.au>
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FlintLabs\Bundle\FlintCMSBundle\Entity;

/**
 *
 * @author camm (camm@flintinteractive.com.au)
 */
class Fragment implements ViewModelContainerInterface
{
    protected $id;
    protected $type;
    protected $data;

    protected $viewData;

    protected $children;
    protected $parent;
    protected $region;
    protected $sortOrder;

    protected $created;
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


 
