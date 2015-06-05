<?php

namespace Nabu\Models;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;


class CounterQueues {

    protected $queueName;
    protected $conn;

    public function __construct($config) {

        $queue = 'media-movie-views';
        $this->conn = new AMQPConnection($config['host'], $config['port'], $config['login'], $config['pass'], $config['vhost']);

    }

    protected function increaseCounter($code, $queueName) {

        $ch = $this->conn->channel();

        $ch->queue_declare($queueName, false, true, false, false);

        $msg = new AMQPMessage($code, ['content_type' => 'text/plain', 'delivery_mode' => 2]);

        $ch->basic_publish($msg, '', $queueName);

        $ch->close();
        $this->conn->close();

    }

    public function increaseViewsCounter($code) {

        $this->increaseCounter($code, 'item-views');

    }

    public function increaseVotesPositive($code) {

        $this->increaseCounter($code, 'item-votes-positive');

    }

    public function increaseVotesNegative($code) {

        $this->increaseCounter($code, 'item-votes-negative');

    }

    public function increaseFavoritesCounter($code) {

        $this->increaseCounter($code, 'item-favorites');

    }

}