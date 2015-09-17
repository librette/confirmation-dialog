<?php
namespace Librette\ConfirmationDialog;

use Nette\Application\UI\Control;

/**
 * @author David Matejka
 */
class ConfirmationDialog extends Control
{

	/** @var string */
	protected $templateFile;

	/** @var string */
	protected $question = 'Are you sure?';

	/** @var bool */
	protected $enabled = FALSE;


	public function __construct()
	{
		$this->templateFile = __DIR__ . '/confirmationDialog.latte';
	}


	protected function createComponentForm()
	{
		$form = new ConfirmationForm();
		$group = $form->addGroup();
		$form->addGroup();
		$form->addHidden('token');
		$form->addSubmit('ok', 'OK');
		$form->addSubmit('cancel', 'Cancel')->setValidationScope(FALSE);
		$form->setCurrentGroup($group);

		return $form;
	}


	/**
	 * @return string
	 */
	public function getQuestion()
	{
		return $this->question;
	}


	/**
	 * @param string
	 * @return self
	 */
	public function setQuestion($question)
	{
		$this->question = $question;

		return $this;
	}


	/**
	 * @param string
	 * @return self
	 */
	public function setTemplateFile($templateFile)
	{
		$this->templateFile = $templateFile;

		return $this;
	}


	/**
	 * @param boolean
	 * @return self
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;

		return $this;
	}


	public function render()
	{
		$this->template->setFile($this->templateFile);
		$this->template->enabled = $this->enabled;
		$this->template->question = $this->question;
		$this->template->render();
	}

}
