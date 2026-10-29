<?php

namespace WCML\MultiCurrency\ExchangeRateServices;

class Fixerio extends ApiLayerService {

	public function getId() {
		return 'fixerio';
	}

	public function getName() {
		return 'Fixer.io';
	}

	public function getUrl() {
		return 'https://fixer.io/';
	}

	protected function getApiLayerUrl() {
		return 'https://api.apilayer.com/fixer/latest?base=%2$s&symbols=%3$s';
	}

	protected function getApiLegacyUrl() {
		return 'https://data.fixer.io/api/latest?access_key=%1$s&base=%2$s&symbols=%3$s';
	}
}
