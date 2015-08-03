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
            error_log('AMQPStreamConnection start');
            $this->conn = new AMQPStreamConnection($config['host'], $config['port'], $config['login'], $config['pass'],
                $config['vhost'], false, 'AMQPLAIN', null, 'en_US', 1, 1, null, true);
            error_log('AMQPStreamConnection stop #1 ');
        } catch(\Exception $e) {
            error_log('AMQPStreamConnection stop #2');
            $this->connectionError = $e;
        }


    }

    protected function increaseCounter($code, $queueName) {

        if($this->connectionError) {
            return;
        }

        error_log('increaseCounter #1');
        $ch = $this->conn->channel();
        error_log('increaseCounter #2');
        $ch->queue_declare($queueName, false, true, false, false);
        error_log('increaseCounter #3');
        $msg = new AMQPMessage($code, ['content_type' => 'text/plain', 'delivery_mode' => 2]);
        error_log('increaseCounter #4');
        $ch->basic_publish($msg, '', $queueName);
        error_log('increaseCounter #5');
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