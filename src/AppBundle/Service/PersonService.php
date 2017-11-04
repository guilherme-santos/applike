<?php

namespace AppBundle\Service;

use AppBundle\Entity\PersonEntity;
use AppBundle\Exception\APIException;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class PersonService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Producer
     */
    protected $brokerProducer;

    /**
     * @param string $name
     * @param \DateTime|string $birthday
     */
    public function createPerson($name = null, $birthday = null, $flush = true)
    {
        if ($birthday && !$birthday instanceof \DateTime) {
            $birthday = \DateTime::createFromFormat('Y-m-d', $birthday);
            if (!$birthday) {
                throw new APIException('invalid_date', 'Use format "YYYY-MM-DD"');
            }
        }

        $person = new PersonEntity();
        $person->setName($name);
        $person->setBirthday($birthday);
        $person->setActive(true);

        $person->validate();

        $entityManager = $this->getEntityManager();
        $entityManager->persist($person);
        if ($flush) {
            $entityManager->flush();
        }

        return true;
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function getOne($id)
    {
        $entityManager = $this->getEntityManager();
        $person = $entityManager->getRepository(PersonEntity::class)->find($id);
        if (!$person) {
            return null;
        }

        return [
            'id' => $person->getId(),
            'name' => $person->getName(),
            'birthday' => $person->getBirthday()->format('Y-m-d'),
            'active' => $person->isActive(),
        ];
    }

    /**
     * @param int $id
     */
    public function send($id)
    {
        $person = $this->getOne($id);
        if (!$person) {
            return false;
        }

        $this->getBrokerProducer()->publish(json_encode($person));
        return true;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        $entityManager = $this->getEntityManager();
        $people = $entityManager->getRepository(PersonEntity::class)->findAll();

        return array_map(function (PersonEntity $person) {
            return [
                'id' => $person->getId(),
                'name' => $person->getName(),
                'birthday' => $person->getBirthday()->format('Y-m-d'),
                'active' => $person->isActive(),
            ];
        }, $people);
    }

    /**
     * @param int $total
     */
    public function loadDummies($total = 100)
    {
        $baseBirthday = new \DateTime('1988-08-12');

        for ($i=0; $i<$total; $i++) {
            $baseBirthday->add(new \DateInterval(sprintf('P%1$dM%1$dD', $i)));
            $birthday = clone $baseBirthday;
            $name = md5($birthday->format(\DateTime::RFC3339_EXTENDED));
            $this->createPerson($name, $birthday, false);
        }

        $this->getEntityManager()->flush();

        return true;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return PersonService
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return Producer
     */
    public function getBrokerProducer()
    {
        return $this->brokerProducer;
    }

    /**
     * @param Producer $brokerProducer
     * @return PersonService
     */
    public function setBrokerProducer($brokerProducer)
    {
        $this->brokerProducer = $brokerProducer;
        return $this;
    }
}
