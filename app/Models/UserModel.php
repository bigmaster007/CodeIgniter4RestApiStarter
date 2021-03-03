<?php

namespace App\Models;

use CodeIgniter\Model;
use Exception;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password'
    ];
    protected $updatedField = 'updated_at';

    protected function beforeInsert(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plainTextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plainTextPassword);
        }

        return $data;
    }

    private function hashPassword(string $plainTextPassword): string
    {
        return password_hash($plainTextPassword, PASSWORD_BCRYPT);
    }

    public function findUserByField(string $field, string $search): array
    {
        $user = $this->asArray()
            ->where($field, $search)
            ->first();

        if (!$user)
            throw new Exception('User doe not exist for specified ' . $field . ' field');
        return $user;
    }
}
