<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable {
    protected $id;

    protected $name;

    protected $email;

    protected $token;

    protected $roles;

    /**
     * User constructor.
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $token
     * @param array $roles
     */
    public function __construct(string $id, string $name, string $email, string $token, array $roles = []) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->token = $token;
        $this->roles = $roles;
    }


    public function getAuthIdentifierName() {
        return $this->email;
    }

    public function getAuthIdentifier() {
        return $this->id;
    }

    public function getAuthPassword() {
        throw new \Exception('Not implemented!');
    }

    public function getRememberToken() {
        throw new \Exception('Not implemented!');
    }

    public function setRememberToken($value) {
        throw new \Exception('Not implemented!');
    }

    public function getRememberTokenName() {
        throw new \Exception('Not implemented!');
    }

    public function getRoles() {
        return $this->roles;
    }

    public function hasRole($role) {
        return in_array($role, $this->roles);
    }
}
