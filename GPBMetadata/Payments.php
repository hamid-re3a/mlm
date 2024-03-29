<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: payments.proto

namespace GPBMetadata;

class Payments
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\User::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
payments.protopayments.services.grpc"
Id

id ("
EmptyObject"v
PaymentType

id (
name (	
	is_active (

deleted_at (	

created_at (	

updated_at (	"J
PaymentTypes:
payment_types (2#.payments.services.grpc.PaymentType"x
PaymentDriver

id (
name (	
	is_active (

deleted_at (	

created_at (	

updated_at (	"�
PaymentCurrency

id (
name (	
	is_active (

deleted_at (	

created_at (	

updated_at (	=
payment_driver (2%.payments.services.grpc.PaymentDriver"X
PaymentCurrenciesC
payment_currencies (2\'.payments.services.grpc.PaymentCurrency"�
PaymentTransaction

id (

invoice_id (
hash (	
received_date (	
value (	
fee (	
status (	
destination (	"�
Invoice
payable_type (	

payable_id (
user_id (
	pf_amount (
amount (
transaction_id (	
checkout_link (	
status (	
additional_status	 (	
paid_amount
 (

due_amount (
is_paid (
expiration_time (	
payment_type (	
payment_currency (	
payment_driver (	

deleted_at (	

created_at (	

updated_at (	&
user (2.user.services.grpc.User
deposit_amount (2�
PaymentsServiceO
getInvoiceById.payments.services.grpc.Id.payments.services.grpc.Invoice" I
pay.payments.services.grpc.Invoice.payments.services.grpc.Invoice" h
getPaymentCurrencies#.payments.services.grpc.EmptyObject).payments.services.grpc.PaymentCurrencies" ^
getPaymentTypes#.payments.services.grpc.EmptyObject$.payments.services.grpc.PaymentTypes" bproto3'
        , true);

        static::$is_initialized = true;
    }
}

