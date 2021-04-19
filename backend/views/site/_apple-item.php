<?php

use yii\helpers\Html;
use yii\bootstrap\Dropdown;
use common\models\Apple;

/* @var $this yii\web\View */
?>

	<img src="/images/apple-<?php echo $model->color;?>.png">

	<?php if ($model->isRotten):?>
		<img class="rotten-stamp" src="/images/rotten.png">
	<?php endif;?>

	<div class="info flex flex-column">
		<?php 
			echo Html::tag('div', $model->colorName . ' яблоко', ['class' => 'name']);
			echo Html::tag('div', 'Найдено ' . date('d.m.Y H:i', $model->created), ['class' => 'size']);
			echo Html::tag('div', $model->status == Apple::STATUS_ON_TREE ? 'Висит на дереве.' : 'Упало ' . date('d.m.Y H:i', $model->fell), ['class' => 'status']);
			echo Html::tag('div', $model->size == 1 ? 'Целое' : 'Осталось ' . $model->size * 100 . '%', ['class' => 'size']);
		?>

		<div class="actions flex">
			<?php 
				if ($model->status == Apple::STATUS_ON_TREE) {
					echo Html::button('Сбить с дерева', ['class' => 'btn btn-primary', 'data-action' => 'fall']);
				} else {
					$items = [];
					if ($model->size == 1){
						$items[] = [
							'label' => 'Съесть целиком',
							'url' => '#',
							'options' => ['data-action' => 'eat', 'data-value' => 100]
						];
					}
					foreach (['половину' => 50, 'треть' => 33, 'четверть' => 25, 'кусочек' => 10] as $key => $value) {
						if ($value > $model->size * 100) continue;
						$items[] = [
							'label' => 'Съесть ' . $key,
							'url' => '#',
							'options' => ['data-action' => 'eat', 'data-value' => $value]
						];
					}
					if ($model->size < 1){
						$items[] = [
							'label' => 'Доесть',
							'url' => '#',
							'options' => ['data-action' => 'eat', 'data-value' => $model->size * 100]
						];
					}

    				echo Html::beginTag('div', ['class' => 'dropdown']);
    				echo Html::a('Съесть...', '#', ['class' => 'btn btn-primary', 'data-toggle' => 'dropdown']);
					echo Dropdown::widget([
			            'items' => $items,
			            'options' => ['id' => 'eat-' . $model->id, 'class' => 'eat-selector']
			        ]);
			        echo Html::endTag('div');
				}

				echo Html::tag('span', '<i class="glyphicon glyphicon-trash"></i>', ['class' => 'delete-apple', 'data-action' => 'delete']);
			?>
		</div>
	</div>
	