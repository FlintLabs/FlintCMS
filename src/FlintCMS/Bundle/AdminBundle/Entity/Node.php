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
use Doctrine\ORM\Mapping as ORM,
Gedmo\Tree\Node as NodeInterface,
Gedmo\Mapping\Annotation as DoctrineExtensions;
/**
 * @ORM\Entity(repositoryClass="FlintCMS\Bundle\AdminBundle\Entity\Repository\NodeRepository")
 * @ORM\Table(name="node")
 * @author camm (camm@flintinteractive.com.au)
 */
class Node implements NodeInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Fragment", inversedBy="node", fetch="EAGER")
     * @var FlintLabs\Bundle\FlintCMSBundle\Entity\Fragment
     */
    protected $fragment;

    /**
     * @ORM\Column(name="label")
     * @var string
     */
    protected $label;

    /**
     * @ORM\DoctrineExtensions\TreeParent
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children", fetch="LAZY")
     * @ORM\JoinColumn(name="parent_node_id", referencedColumnName="node_id", nullable="true")
     * @var Node
     */
    protected $parent;
    protected $root;

    /**
     * @ORM\DoctrineExtensions\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     * @var int
     */
    protected $left;

    /**
     * @ORM\DoctrineExtensions\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     * @var int
     */
    protected $right;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     * @var array
     */
    protected $children;

    
    protected $depth;

    protected $created;
    protected $user;


    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setDepth($depth)
    {
        $this->depth = $depth;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLeft($left)
    {
        $this->left = $left;
    }

    public function getLeft()
    {
        return $this->left;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setRight($right)
    {
        $this->right = $right;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function setRoot($root)
    {
        $this->root = $root;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
