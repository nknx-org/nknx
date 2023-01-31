<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protobuf/protos/transaction.proto

namespace Protos;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>protos.GenerateID</code>
 */
class GenerateID extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bytes public_key = 1;</code>
     */
    protected $public_key = '';
    /**
     * Generated from protobuf field <code>int64 registration_fee = 2;</code>
     */
    protected $registration_fee = 0;
    /**
     * Generated from protobuf field <code>bytes sender = 3;</code>
     */
    protected $sender = '';
    /**
     * Generated from protobuf field <code>int32 version = 4;</code>
     */
    protected $version = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $public_key
     *     @type int|string $registration_fee
     *     @type string $sender
     *     @type int $version
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protobuf\Protos\Transaction::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bytes public_key = 1;</code>
     * @return string
     */
    public function getPublicKey()
    {
        return $this->public_key;
    }

    /**
     * Generated from protobuf field <code>bytes public_key = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setPublicKey($var)
    {
        GPBUtil::checkString($var, False);
        $this->public_key = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 registration_fee = 2;</code>
     * @return int|string
     */
    public function getRegistrationFee()
    {
        return $this->registration_fee;
    }

    /**
     * Generated from protobuf field <code>int64 registration_fee = 2;</code>
     * @param int|string $var
     * @return $this
     */
    public function setRegistrationFee($var)
    {
        GPBUtil::checkInt64($var);
        $this->registration_fee = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes sender = 3;</code>
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Generated from protobuf field <code>bytes sender = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setSender($var)
    {
        GPBUtil::checkString($var, False);
        $this->sender = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 version = 4;</code>
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Generated from protobuf field <code>int32 version = 4;</code>
     * @param int $var
     * @return $this
     */
    public function setVersion($var)
    {
        GPBUtil::checkInt32($var);
        $this->version = $var;

        return $this;
    }

}
