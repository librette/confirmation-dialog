<?php
namespace Librette\ConfirmationDialog;

/**
 * @author David Matejka
 */
interface ConfirmationDialogFactory
{

	/**
	 * @return ConfirmationDialog
	 */
	public function create();
}
