<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

class TopListPresenter extends BasePresenter {

	const TBL_ITEMS_COUNT = 20;

	/** @var \App\Services\DataProvider\TopList @inject */
	var $topListProvider;

	public function actionDefault() {
		$result = $this->topListProvider->getData([], self::TBL_ITEMS_COUNT);
		$this->template->players = $result;
	}
}
