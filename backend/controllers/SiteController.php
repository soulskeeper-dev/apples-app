<?php
namespace backend\controllers;

use Yii;
use yii\base\UserException;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\VerifyEmailForm;
use common\models\Apple;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'apple', 'get-apples'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'apple' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'blank'
            ],
        ];
    }

    /**
     * Основаная страница
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('login');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => Apple::find(),
            'pagination' => [
                'pageSize' => 21,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Действия с яблоком
     * @return string [description]
     */
    public function actionApple()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        if (!isset($post['id']) || !$post['id'] || !$apple = Apple::findOne($post['id'])) {
            return ['result' => 'error', 'message' => 'Яблоко потерялось... Странно...'];
        }

        try {
            switch ($post['action']) {
                case 'fall':
                    $apple->fallToGround();
                    break;
                case 'eat':
                    $apple->eat($post['value'] ?? 0);
                    break;
                case 'delete':
                    $apple->delete();
                    break;
                
                default:
                    return ['result' => 'error', 'message' => 'Что сделать?'];
            }
        } catch (\Exception $e) {
            return ['result' => 'error', 'message' => $e->getMessage()];
        }

        $result = ['result' => 'ok'];
        if (!$apple->size || $post['action'] == 'delete') {
            $result['refresh'] = true;
        } else {
            $result['content'] = $this->renderPartial('_apple-item', ['model' => $apple]);
        }

        return $result;
    }

    /**
     * Добавление яблок
     * @return string
     */
    public function actionGetApples()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        Apple::getSomeApples();
        return ['result' => 'ok'];
    }

    /**
     * Авторизация
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выход
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
