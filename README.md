Installation
------------

The best way to install this extension is using  [Composer](http://getcomposer.org/):

```sh
$ composer require librette/confirmation-dialog
```

Usage
-----------

You can use confirmation dialog in handle* methods in presenters and controls.

```php
class ArticleControl extends Control
{
	use Librette\ConfirmationDialog\TConfirmation;


	public function handleRemove($id)
	{
		$article = $this->articleRepository->get($id);
		if($this->confirm("Do you really want to delete article {$article->title}?")) {
			$this->articleRepository->delete($article);
			$this->redirect('this');
		} elseif($this->isConfirmationCancelled()) {
			$this->redirect('this');
		}
	}

}
```
