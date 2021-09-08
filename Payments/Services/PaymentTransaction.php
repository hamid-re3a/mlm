<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: payments.proto

namespace Payments\Services;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>payments.services.PaymentTransaction</code>
 */
class PaymentTransaction extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int64 id = 1;</code>
     */
    protected $id = 0;
    /**
     * Generated from protobuf field <code>int64 invoice_id = 2;</code>
     */
    protected $invoice_id = 0;
    /**
     * Generated from protobuf field <code>string hash = 3;</code>
     */
    protected $hash = '';
    /**
     * Generated from protobuf field <code>string received_date = 4;</code>
     */
    protected $received_date = '';
    /**
     * Generated from protobuf field <code>string value = 5;</code>
     */
    protected $value = '';
    /**
     * Generated from protobuf field <code>string fee = 6;</code>
     */
    protected $fee = '';
    /**
     * Generated from protobuf field <code>string status = 7;</code>
     */
    protected $status = '';
    /**
     * Generated from protobuf field <code>string destination = 8;</code>
     */
    protected $destination = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int|string $id
     *     @type int|string $invoice_id
     *     @type string $hash
     *     @type string $received_date
     *     @type string $value
     *     @type string $fee
     *     @type string $status
     *     @type string $destination
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Payments::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int64 id = 1;</code>
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Generated from protobuf field <code>int64 id = 1;</code>
     * @param int|string $var
     * @return $this
     */
    public function setId($var)
    {
        GPBUtil::checkInt64($var);
        $this->id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 invoice_id = 2;</code>
     * @return int|string
     */
    public function getInvoiceId()
    {
        return $this->invoice_id;
    }

    /**
     * Generated from protobuf field <code>int64 invoice_id = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setInvoiceId($var)
    {
        GPBUtil::checkInt64($var);
        $this->invoice_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string hash = 3;</code>
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Generated from protobuf field <code>string hash = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setHash($var)
    {
        GPBUtil::checkString($var, True);
        $this->hash = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string received_date = 4;</code>
     * @return string
     */
    public function getReceivedDate()
    {
        return $this->received_date;
    }

    /**
     * Generated from protobuf field <code>string received_date = 4;</code>
     * @param string $var
     * @return $this
     */
    public function setReceivedDate($var)
    {
        GPBUtil::checkString($var, True);
        $this->received_date = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string value = 5;</code>
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Generated from protobuf field <code>string value = 5;</code>
     * @param string $var
     * @return $this
     */
    public function setValue($var)
    {
        GPBUtil::checkString($var, True);
        $this->value = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string fee = 6;</code>
     * @return string
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Generated from protobuf field <code>string fee = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setFee($var)
    {
        GPBUtil::checkString($var, True);
        $this->fee = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string status = 7;</code>
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Generated from protobuf field <code>string status = 7;</code>
     * @param string $var
     * @return $this
     */
    public function setStatus($var)
    {
        GPBUtil::checkString($var, True);
        $this->status = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string destination = 8;</code>
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Generated from protobuf field <code>string destination = 8;</code>
     * @param string $var
     * @return $this
     */
    public function setDestination($var)
    {
        GPBUtil::checkString($var, True);
        $this->destination = $var;

        return $this;
    }

}
