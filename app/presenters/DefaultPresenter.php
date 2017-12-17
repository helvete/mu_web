<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

class DefaultPresenter extends BasePresenter {

	public function actionDefault() {
		echo "Das ist Default!";
		$this->terminate();
	}
}
