<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Services\MupiClient;

class HomepagePresenter extends Nette\Application\UI\Presenter {

	/** @var \App\Services\DataProvider\TopList @inject */
	var $topListProvider;

	public function actionDefault() {
		$result = $this->topListProvider->getCharacter();
		$this->template->code = key($result);
		$this->template->players = current($result);
	}
}
