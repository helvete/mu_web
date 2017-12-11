<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Services\MupiClient;

class HomepagePresenter extends Nette\Application\UI\Presenter {

	const TBL_ITEMS_COUNT = 20;

	/** @var \App\Services\DataProvider\TopList @inject */
	var $topListProvider;

	public function actionDefault() {
		$result = $this->topListProvider->getData([], self::TBL_ITEMS_COUNT);
		$this->template->players = $result;
	}
}
