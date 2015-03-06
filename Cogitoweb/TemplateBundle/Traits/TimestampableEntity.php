<?php

namespace Cogitoweb\TemplateBundle\Traits;

/**
 * Timestampable Trait, usable with PHP >= 5.4
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
trait TimestampableEntity
{
    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * Sets createdAt.
     *
     * @param  mixed $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        
        if(!($createdAt instanceof \DateTime)) {
            $createdAt = new \DateTime();
        }
        
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param  mixed $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        
        if(!($updatedAt instanceof \DateTime)) {
            $updatedAt = new \DateTime();
        }
        
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
