<?php

namespace Dcat\Utils\Elasticsearch\Connections;

use Elasticsearch\Connections\ConnectionInterface;
use Elasticsearch\Connections\ConnectionFactoryInterface;
use Elasticsearch\Serializers\SerializerInterface;
use Psr\Log\LoggerInterface;


/**
 * Class AbstractConnection
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Connections
 * @author   jqh
 */
class ConnectionFactory implements ConnectionFactoryInterface
{
    /** @var  array */
    private $connectionParams;

    /** @var  SerializerInterface */
    private $serializer;

    /** @var  LoggerInterface */
    private $logger;

    /** @var  LoggerInterface */
    private $tracer;

    /** @var callable */
    private $handler;

    /**
     * Constructor
     *
     * @param callable            $handler
     * @param array               $connectionParams
     * @param SerializerInterface $serializer
     * @param LoggerInterface     $logger
     * @param LoggerInterface     $tracer
     */
    public function __construct(callable $handler, array $connectionParams, SerializerInterface $serializer, LoggerInterface $logger, LoggerInterface $tracer)
    {
        $this->handler          = $handler;
        $this->connectionParams = $connectionParams;
        $this->logger           = $logger;
        $this->tracer           = $tracer;
        $this->serializer       = $serializer;
    }
    /**
     * @param $hostDetails
     *
     * @return ConnectionInterface
     */
    public function create(array $hostDetails): ConnectionInterface
    {
        return new HttpConnection(
            $this->handler,
            $hostDetails,
            $this->connectionParams,
            $this->serializer,
            $this->logger,
            $this->tracer
        );
    }

}