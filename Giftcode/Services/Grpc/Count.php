<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: giftcodes.proto

namespace Giftcode\Services\Grpc;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>giftcode.services.grpc.Count</code>
 */
class Count extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int64 counts = 1;</code>
     */
    protected $counts = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $counts
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Giftcodes::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int64 counts = 1;</code>
     * @return int|string
     */
    public function getCounts()
    {
        return $this->counts;
    }

    /**
     * Generated from protobuf field <code>int64 counts = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setCounts($var)
    {
        GPBUtil::checkInt64($var);
        $this->counts = $var;

        return $this;
    }

}
