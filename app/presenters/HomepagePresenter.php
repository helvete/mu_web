<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
	public function actionTest() {
		echo "test!!";
		$this->terminate();
	}
}
