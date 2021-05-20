<?php

namespace WcPay360\Api\Exceptions;

class Response_Exception extends \Exception {

	protected $pay360_return_url;
	protected $pay360_order = false;
	protected $pay360_order_id = 0;

	public function get_order() {
		if ( ! $this->pay360_order instanceof \WC_Order && 0 < $this->get_order_id() ) {
			$this->pay360_order = wc_get_order( $this->get_order_id() );
		}

		return $this->pay360_order;
	}

	public function get_order_id() {
		return (int) $this->pay360_order_id;
	}

	public function set_order( \WC_Order $order ) {
		$this->pay360_order = $order;
	}

	public function set_order_id( $order_id ) {
		$this->pay360_order_id = (int) $order_id;
	}

	public function get_return_url() {
		return $this->pay360_return_url;
	}

	public function set_return_url( $url ) {
		$this->pay360_return_url = $url;
	}
}
