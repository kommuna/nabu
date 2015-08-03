<?php

namespace Nabu\Models;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class CounterQueues {

    protected $queueName;
    protected $conn;
    protected $connectionError;

    public function __construct($config) {

        try {
            if(empty($config['host'])) {
                throw new \Exception('RabbitMQ disabled or not configured');
            }
            $this->conn = new AMQPStreamConnection($config['host'], $config['port'], $config['login'], $config['pass'],
                $config['vhost'], false, 'AMQPLAIN', null, 'en_US', 1, 1, null, true);
        } catch(\Exception $e) {
            $this->connectionError = $e;
        }


    }

    protected function increaseCounter($code, $queueName) {

        if($this->connectionError) {
            return;
        }

        $ch = $this->conn->channel();
        $ch->queue_declare($queueName, false, true, false, false);
        $msg = new AMQPMessage($code, ['content_type' => 'text/plain', 'delivery_mode' => 2]);
        $ch->basic_publish($msg, '', $queueName);
        $ch->close();
        $this->conn->close();

    }

    public function increaseViewsCounter($code) {

        $this->increaseCounter($code, 'nabu-item-views');

    }

    public function increaseVotesPositive($code) {

        $this->increaseCounter($code, 'nabu-item-votes-positive');

    }

    public function increaseVotesNegative($code) {

        $this->increaseCounter($code, 'nabu-item-votes-negative');

    }

    public function increaseFavoritesCounter($code) {

        $this->increaseCounter($code, 'nabu-item-favorites');

    }

}