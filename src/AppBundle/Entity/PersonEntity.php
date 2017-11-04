<?php

namespace AppBundle\Entity;


use AppBundle\Exception\APIException;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PersonEntity
 * @package AppBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="people")
 */
class PersonEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $birthday;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PersonEntity
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     * @return PersonEntity
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return PersonEntity
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (mb_strlen($this->getName()) === 0) {
            throw new APIException('missing_name', 'No name was informed');
        }

        if (mb_strlen($this->getName()) < 3) {
            throw new APIException('missing_name', 'Invalid name expected at least 3 characters');
        }

        if (!$this->getBirthday() instanceof \DateTime) {
            throw new APIException('missing_birthday', 'Invalid birthday');
        }
    }
}
