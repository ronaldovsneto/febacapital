<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string $name
 * @property string $cpf
 * @property string $sex
 * @property string|null $zipcode
 * @property string|null $address
 * @property string|null $number
 * @property string|null $neighborhood
 * @property string|null $city
 * @property string|null $state
 * @property string|null $complement
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'cpf', 'sex'], 'required'],
            [['sex'], 'string'],
            ['sex', 'in', 'range' => ['M', 'F']],
            [['name', 'cpf', 'zipcode', 'address', 'number', 'neighborhood', 'city', 'state', 'complement'], 'string', 'max' => 255],
            [['cpf'], 'unique'],
            ['cpf', 'match', 'pattern' => '/^\d{11}$/', 'message' => 'CPF deve conter 11 dígitos.'],
            ['zipcode', 'match', 'pattern' => '/^\d{8}$/', 'message' => 'CEP deve conter 8 dígitos.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nome',
            'cpf' => 'Cpf',
            'sex' => 'Sexo',
            'zipcode' => 'CEP',
            'address' => 'Endereço',
            'number' => 'Numéro',
            'neighborhood' => 'Bairro',
            'city' => 'Cidade',
            'state' => 'Estado',
            'complement' => 'Complemento',
        ];
    }
}
