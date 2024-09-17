<?php

namespace app\controllers;

use app\models\Book;
use Exception;
use GuzzleHttp\Client;
use Yii;
use yii\data\ActiveDataProvider;

class BookController extends BaseController
{

    public function actionCreate()
    {
        $request = Yii::$app->request->post();
        $validation = $this->verifyIsbn($request['isbn']);
        if ($validation['success']) {
            $book = new Book();
            $data = $validation['data'];
            $book->isbn = $request['isbn'];
            $book->author = implode(',', $data['authors']);
            $book->title = $data['title'];
            $book->price = $request['price'];
            $book->inventory = $request['inventory'];

            $book->save();
            return $this->asJson([
                'Status' => 'Livro criado com sucesso!',
                'Livro' => $book,
            ]);
        }

        return $this->asJson([
            'Status' => 'Falha ao tentar criar o livro',
            'Error' => $validation['error'],
        ]);
    }

    private function verifyIsbn($isbn)
    {
        try {
            $client = new Client();
            $response = $client->get('https://brasilapi.com.br/api/isbn/v1/' . $isbn);
            if ($response->getStatusCode() === 200) {
                $content = $response->getBody()->getContents();
                return [
                    'success' => true,
                    'code' => $response->getStatusCode(),
                    'data' => json_decode($content, true)
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'message' => explode("\n", $e->getMessage())[0]
                ]
            ];
        }

    }

    public function actionList()
    {
        $query = Book::find();
        $filter = Yii::$app->request->get('filter', '');
        $limit = Yii::$app->request->get('limit', 10);
        $orderBy = Yii::$app->request->get('orderBy');
        $orderDirection = Yii::$app->request->get('orderDirection', 'asc');

        if ($filter) {
            $query
                ->orWhere(['like', 'title', $filter])
                ->orWhere(['like', 'author', $filter])
                ->orWhere(['=', 'isbn', $filter]);
        }

        $allowedSortFields = ['title', 'author', 'isbn'];
        $sort = ['title' => SORT_ASC];

        if ($orderBy) {
            if (in_array($orderBy, $allowedSortFields)) {
                $orderDirection = ($orderDirection === 'desc') ? SORT_DESC : SORT_ASC;
                $sort = [$orderBy => $orderDirection];
            }
        }


        $requestProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit,
            ],
            'sort' => [
                'attributes' => $allowedSortFields,
                'defaultOrder' => $sort,
            ],
        ]);

        return $this->asJson([
            'Status' => 'success',
            'Livros' => $requestProvider->getModels(),
            'Total' => $requestProvider->getTotalCount(),
        ]);
    }

}
