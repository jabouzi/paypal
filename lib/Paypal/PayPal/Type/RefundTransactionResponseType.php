<?php
/**
 * @package PayPal
 */

/**
 * Make sure our parent class is defined.
 */
require_once 'PayPal/Type/AbstractResponseType.php';

/**
 * RefundTransactionResponseType
 *
 * @package PayPal
 */
class RefundTransactionResponseType extends AbstractResponseType
{
    /**
     * Unique transaction ID of the refund.
     */
    var $RefundTransactionID;

    /**
     * Amount subtracted from PayPal balance of original recipient of payment to make
     * this refund
     */
    var $NetRefundAmount;

    /**
     * Transaction fee refunded to original recipient of payment
     */
    var $FeeRefundAmount;

    /**
     * Amount of money refunded to original payer
     */
    var $GrossRefundAmount;

    /**
     * Total of all previous refunds
     */
    var $TotalRefundedAmount;

    var $RefundInfo;

    /**
     * Any general information like offer details that is reinstated or any other
     * marketing data
     */
    var $ReceiptData;

    function RefundTransactionResponseType()
    {
        parent::AbstractResponseType();
        $this->_namespace = 'urn:ebay:api:PayPalAPI';
        $this->_elements = array_merge($this->_elements,
            array (
              'RefundTransactionID' => 
              array (
                'required' => false,
                'type' => 'TransactionId',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'NetRefundAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'FeeRefundAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'GrossRefundAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'TotalRefundedAmount' => 
              array (
                'required' => false,
                'type' => 'BasicAmountType',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
              'RefundInfo' => 
              array (
                'required' => false,
                'type' => 'RefundInfoType',
                'namespace' => 'urn:ebay:apis:eBLBaseComponents',
              ),
              'ReceiptData' => 
              array (
                'required' => false,
                'type' => 'string',
                'namespace' => 'urn:ebay:api:PayPalAPI',
              ),
            ));
    }

    function getRefundTransactionID()
    {
        return $this->RefundTransactionID;
    }
    function setRefundTransactionID($RefundTransactionID, $charset = 'iso-8859-1')
    {
        $this->RefundTransactionID = $RefundTransactionID;
        $this->_elements['RefundTransactionID']['charset'] = $charset;
    }
    function getNetRefundAmount()
    {
        return $this->NetRefundAmount;
    }
    function setNetRefundAmount($NetRefundAmount, $charset = 'iso-8859-1')
    {
        $this->NetRefundAmount = $NetRefundAmount;
        $this->_elements['NetRefundAmount']['charset'] = $charset;
    }
    function getFeeRefundAmount()
    {
        return $this->FeeRefundAmount;
    }
    function setFeeRefundAmount($FeeRefundAmount, $charset = 'iso-8859-1')
    {
        $this->FeeRefundAmount = $FeeRefundAmount;
        $this->_elements['FeeRefundAmount']['charset'] = $charset;
    }
    function getGrossRefundAmount()
    {
        return $this->GrossRefundAmount;
    }
    function setGrossRefundAmount($GrossRefundAmount, $charset = 'iso-8859-1')
    {
        $this->GrossRefundAmount = $GrossRefundAmount;
        $this->_elements['GrossRefundAmount']['charset'] = $charset;
    }
    function getTotalRefundedAmount()
    {
        return $this->TotalRefundedAmount;
    }
    function setTotalRefundedAmount($TotalRefundedAmount, $charset = 'iso-8859-1')
    {
        $this->TotalRefundedAmount = $TotalRefundedAmount;
        $this->_elements['TotalRefundedAmount']['charset'] = $charset;
    }
    function getRefundInfo()
    {
        return $this->RefundInfo;
    }
    function setRefundInfo($RefundInfo, $charset = 'iso-8859-1')
    {
        $this->RefundInfo = $RefundInfo;
        $this->_elements['RefundInfo']['charset'] = $charset;
    }
    function getReceiptData()
    {
        return $this->ReceiptData;
    }
    function setReceiptData($ReceiptData, $charset = 'iso-8859-1')
    {
        $this->ReceiptData = $ReceiptData;
        $this->_elements['ReceiptData']['charset'] = $charset;
    }
}
