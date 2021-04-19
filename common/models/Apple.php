<?php
namespace common\models;

use Yii;
use yii\base\UserException;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * Apple model
 *
 * @property integer $id
 * @property string $color
 * @property integer $status
 * @property integer $created
 * @property integer $fell
 */
class Apple extends ActiveRecord
{
	const COLORS = ['green', 'red', 'yellow'];
	const STATUS_ON_TREE = 0;
    const STATUS_FELL = 1;

    /**
     * Конструктор
     *
     * @param string $color
     */
    public function __construct(string $color = null)
    {
    	$this->setColor($color);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%apple}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        	['color', 'in', 'range' => self::COLORS],
        	[['created', 'fell'], 'integer', 'min' => strtotime('2021-04-01'), 'max' => time()],
        	['status', 'in', 'range' => [self::STATUS_ON_TREE, self::STATUS_FELL]],
        	['status', 'default', 'value' => self::STATUS_ON_TREE],
        	['size', 'double', 'min' => 0, 'max' => 1],
            ['size', 'default', 'value' => 1],
            
        ];
    }

    /**
     * Выполняет действия перед удаление
     * @throws UserException
     * @return boolean
     */
    public function beforeDelete()
	{
	    if (!parent::beforeDelete()) {
	        return false;
	    }

	    if ($this->size && !$this->isRotten){
    		throw new UserException('Яблоко еще не съедено');
    	}
	    return true;
	}

    /**
     * Возвращает статус испорченности яблока
     * @return boolean
     */
    public function getIsRotten()
    {
    	return $this->status == self::STATUS_FELL && $this->fell - $this->created >= 5*60*60;
    }

    /**
     * Устанваливает цвет яблока
     * @param string $color цвет
     * @throws InvalidArgumentException
     */
    public function setColor($color)
    {
        $color = trim($color);

        if (!$color) {
            $color = self::COLORS[array_rand(self::COLORS, 1)];
        }

        if (!in_array($color, self::COLORS)){
            throw new InvalidArgumentException(sprintf('Яблок цвета "%s" у нас нет. Есть цвета %s', $color, implode(', ', self::COLORS)));
        }

        $this->color = $color;
    }

    /**
     * Роняет яблоко на землю
     * @throws UserException
     * @return boolean Успешность
     */
    public function fallToGround()
    {
    	if ($this->status == self::STATUS_FELL){
    		throw new UserException('Яблоко уже на земле');
    	}

    	$this->status = self::STATUS_FELL;
    	$this->fell = time();
    	return $this->save();
    }

    /**
     * Съедает заданную часть яблока
     * @throws InvalidArgumentException
     * @throws UserException
     * @param  int    $percent Часть в процентах
     */
    public function eat(int $percent)
    {
    	if ($this->status == self::STATUS_ON_TREE){
    		throw new UserException('Яблоко висит высоко, не достать');
    	}

    	if ($this->isRotten){
    		throw new UserException('Яблоко испортилось. Не стоит такое есть');
    	}

    	if ($percent <= 0) {
    		throw new InvalidArgumentException('Нельзя съесть меньше, чем ничего');
    	}

    	if ($percent > 100) {
    		throw new InvalidArgumentException('Это больше, чем целое яблоко');
    	}

    	$this->size = round($this->size - $percent / 100, 2);
    	if ($this->size < 0) {
    		throw new InvalidArgumentException('Осталось меньше, чем вы хотите съесть');
    	}

    	if ($this->size == 0) {
    		$this->delete();
    	} else {
    		$this->save();
    	}
    }

    /**
     * Генерирует новые яблоки
     * @param  integer $count Количество новых яблок (необязательное)
     */
    public static function getSomeApples($count = null)
    {
    	if (!$count) $count = mt_rand(1, 10);
    	for ($i = 0; $i < $count; $i++) { 
    		$apple = new self();

    		$apple->created = mt_rand(strtotime('2021-04-01'), time());

    		// Некоторые яблоки уже упали
    		if (mt_rand(1, 10) > 8){
    			$apple->status = self::STATUS_FELL;
    			$apple->fell = mt_rand($apple->created, time());
    		}

    		$apple->save();
    	}
    }
}