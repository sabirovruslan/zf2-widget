zf2-widget
==========

If you had an experience with Yii2 (or Yii 1.11), you could know about Widgets.
In Yii Framework, widgets are used to create a part of view in your page. The advantage is that you may open an widget, write the code and close at the final.

If you want to use a piece of view in a big quantity of pages, this is perfect for you!

For example, if you are using the Twitter Bootstrap and in all pages you use a Panel with an icon before the title. And you want to write a big text in the content (panel-body) you can make this.

```php
namespace Application\Widget;

use TokenPost\Widget\Widget;

class Panel
{
  public $title;
  public $icon;

  public function init()
  {
    echo '<div class="panel panel-default">';
      echo '<div class="panel-heading"><span class="glyphicon glyphicon-' . $this->icon . '"></span>' . $this->title . '</div>';
      echo '<div class="panel-body">';
  }
  
  public function run()
  {
      echo '</div>';
    echo '</div>';
  }
}
```

When you want to use in your view, you can use this:

```php
<?php use Application\Widget\Panel; ?>

<?php Panel::begin(['title' => 'My Panel', 'icon' => 'cloud']) ?>

<!-- Here you will write your code.... -->

<?php Panel::end() ?>

```
