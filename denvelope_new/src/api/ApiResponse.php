<?php

namespace Denvelope\Api;

class ApiResponse
{
    private $response;

    public function __construct(string $object, int $status, array $data = [], array $errors = [])
    {
        $this->response["success"] = $status === ApiStatus::OK && count($errors) === 0;

        $this->response["data"] = $data;
        $this->response["errors"] = $errors;
    }

    public function __serialize() : array
    {
        return $this->response;
    }
}