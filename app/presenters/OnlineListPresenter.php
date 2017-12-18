<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

class OnlineListPresenter extends BasePresenter {

	/** @var \App\Services\DataProvider\OnlineList @inject */
	var $onlineListProvider;

	public function actionDefault() {
		$result = $this->onlineListProvider->getData([]);
		$this->template->players = $result;
	}
}
