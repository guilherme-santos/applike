<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 11/5/17
 * Time: 00:26
 */

namespace AppBundle\Service;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class PersonConsumerService implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     */
    public function execute(AMQPMessage $msg)
    {
        $person = json_decode($msg->getBody(), true);

        echo sprintf(
            'id="%d" name="%s" birthday="%s" active="%s"]',
            $person['id'],
            $person['name'],
            $person['birthday'],
            $person['active']
        ) . PHP_EOL;

        return true;
    }
}
