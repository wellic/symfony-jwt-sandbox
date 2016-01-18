<?php

namespace App\SportBundle\Entity;

use App\Util\Doctrine\Entity\AbstractEntity;
use App\Util\Doctrine\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Category.
 *
 * @ORM\Entity
 * @ORM\Table(name="categories_sport")
 * @UniqueEntity("name")
 *
 * @JMS\ExclusionPolicy("all")
 */
class Category extends AbstractEntity implements EntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @JMS\Expose
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Sport", mappedBy="categories")
     */
    protected $sports;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->sports = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: 'Nouvelle Catégorie';
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add sport.
     *
     * @param \App\SportBundle\Entity\Sport $sport
     *
     * @return Category
     */
    public function addSport(\App\SportBundle\Entity\Sport $sport)
    {
        $this->sports[] = $sport;

        return $this;
    }

    /**
     * Remove sport.
     *
     * @param \App\SportBundle\Entity\Sport $sport
     */
    public function removeSport(\App\SportBundle\Entity\Sport $sport)
    {
        $this->sports->removeElement($sport);
    }

    /**
     * Get sports.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSports()
    {
        return $this->sports;
    }
}