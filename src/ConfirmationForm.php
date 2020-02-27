<?php

namespace Librette\ConfirmationDialog;

use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Controls;
use Nette\Forms\Form;
use Nette\Http\IRequest;

/**
 * @author David Matejka
 */
class ConfirmationForm extends Form
{

	/**
	 * @param IRequest|null
	 */
	public function __construct(IRequest $httpRequest = null)
	{
		parent::__construct();
		$this->httpRequest = $httpRequest;
	}


	protected function validateParent(IContainer $parent): void
	{
		parent::validateParent($parent);
		$this->monitor(Presenter::class, function (Presenter $obj) {
			if ($this->httpRequest === null) {
				$this->httpRequest = $obj->getContext()->getByType(IRequest::class);
			}
			$this->getElementPrototype()->action = substr($this->httpRequest->getUrl(), strlen($this->httpRequest->getUrl()->hostUrl));
			$name = $this->lookupPath(Presenter::class);
			if (!isset($this->getElementPrototype()->id)) {
				$this->getElementPrototype()->id = 'frm-' . $name;
			}
			$this[self::TRACKER_ID] = (new Controls\HiddenField($name))->setOmitted();
			if (iterator_count($this->getControls()) && $this->isSubmitted()) {
				foreach ($this->getControls() as $control) {
					if (!$control->isDisabled()) {
						$control->loadHttpData();
					}
				}
			}
		});
	}


	public function isAnchored(): bool
	{
		return (bool) $this->lookup(Presenter::class, false);
	}

}
