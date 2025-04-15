<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Kreait\Firebase\Auth;

class FirebaseService
{
    protected Database $database;
    protected Auth $auth;
    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->withDatabaseUri(config('firebase.database_url'));

        $this->database = $factory->createDatabase();
        $this->auth = $factory->createAuth();
    }

    public function updatePaymentStatus(string $userId, string $orderId, array $data): void
    {
        $this->database
            ->getReference("payments/{$userId}/{$orderId}")
            ->set($data);
    }
    public function getUserByFirebaseUid(string $firebaseUid)
    {
        try {
            return $this->auth->getUser($firebaseUid);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return null;
        }
    }

    public function getUserByEmail(string $email)
    {
        try {
            return $this->auth->getUserByEmail($email);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            return null;
        }
    }
}
