<?php
namespace Librette\ConfirmationDialog;

use Nette;
use Nette\Application\UI\Control;

/**
 * @author David Matejka
 *
 * @method string getMessage()
 * @method setMessage(string $message)
 * @method setTemplateFile(string $templateFile)
 * @method string getTemplateFile()
 * @method setEnabled(bool $enabled)
 */
class ConfirmationDialog extends Control
{

	/** @var string */
	protected $templateFile;

	/** @var string */
	protected $message = 'Are you sure?';

	/** @var bool */
	protected $enabled = FALSE;


	public function __construct()
	{
		$this->templateFile = __DIR__ . '/confirmationDialog.latte';
	}


	protected function createComponentForm()
	{
		$form = new ConfirmationForm();
		$form->addHidden('token');
		$form->addSubmit('ok', 'OK');
		$form->addSubmit('cancel', 'Cancel');

		return $form;
	}


	public function render()
	{
		if (!$this->enabled) {
			return;
		}
		$this->template->setFile($this->templateFile);
		$this->template->message = $this->message;
		$this->template->render();
	}
}