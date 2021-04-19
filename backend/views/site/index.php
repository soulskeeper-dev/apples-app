<?php

use yii\widgets\ListView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Яблочный сад';
?>

    <div class="top-panel">
        <button id="more-apples" class="btn btn-success">Больше яблок!</button>
    </div>

    <?php echo ListView::widget([
        'options' => ['id' => 'apples-container', 'class' => 'flex'],
        'dataProvider' => $dataProvider,
        'itemView' => '_apple-item',
        'itemOptions' => ['class' => 'apple-item flex'],
        'emptyText' => 'Яблок нет. Нажмите кнопку',
        'emptyTextOptions' => ['class' => 'empty-result'],
        'summary' => Html::tag('div', 'с {begin} по {end} из {totalCount}', ['class' => 'summary']),
    ]);?>