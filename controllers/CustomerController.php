<?php

namespace app\controllers;

use app\models\Customer;
use Exception;
use GuzzleHttp\Client;
use Yii;
use yii\data\ActiveDataProvider;

class CustomerController extends BaseController
{

    public function actionCreate()
    {
        $customer = new Customer();
        $request = Yii::$app->request->post();
        $customer->load($request, '');
        $validation = $this->validateZipcode($request['zipcode']);
        if ($validation['success']) {
            $data = $validation['data'];
            $customer->city = $data['city'];
            $customer->state = $data['state'];
            $customer->neighborhood = (isset($request['neighborhood']) && $request['neighborhood'] !== '') ? $request['neighborhood'] : $data['neighborhood'];
            $customer->address = (isset($request['address']) && $request['address'] !== '') ? $request['address'] : $data['street'];
        }

        $customer->save();
        return $this->asJson([
            'Status' => 'success',
            'Cliente' => $customer,
        ]);

    }

    private function validateZipcode($zipcode)
    {
        try {
            $client = new Client();
            $response = $client->get("https://brasilapi.com.br/api/cep/v1/" . $zipcode);

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

        return false;
    }

    public function actionList()
    {
        $query = Customer::find();
        $filter = Yii::$app->request->get('filter', '');
        $limit = Yii::$app->request->get('limit', 10);
        $orderBy = Yii::$app->request->get('orderBy');
        $orderDirection = Yii::$app->request->get('orderDirection', 'asc');

        if ($filter) {
            $query->orWhere(['like', 'name', $filter])->orWhere(['like', 'cpf', $filter]);
        }

        $allowedSortFields = ['name', 'cpf', 'city', 'id'];

        $query->orderBy(['id' => SORT_ASC]);

        if ($orderBy) {
            if (in_array($orderBy, $allowedSortFields)) {
                $orderDirection = ($orderDirection === 'desc') ? SORT_DESC : SORT_ASC;
                $query->orderBy([$orderBy => $orderDirection]);
            }
        }

        $requestProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $limit,
            ],
        ]);

        return $this->asJson([
            'Status' => 'success',
            'Clientes' => $requestProvider->getModels(),
            'Total' => $requestProvider->getTotalCount(),
        ]);
    }

}
