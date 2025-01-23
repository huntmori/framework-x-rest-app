<?php
namespace Damoyo\Api\Common\Dto;

use JsonSerializable;

class ResponseDto implements JsonSerializable {

    public int $code;
    
    public bool $result;
    
    public string $message = '';
    
    public mixed $data;

    public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }

    public static function toResponse(ResponseDto $data) 
    {
        return \React\Http\Message\Response::json($data);
    }

    public static function init() : ResponseDto
    {
        return new self();
    }

    public function result(bool $result) 
    {
        $this->result = $result;
        return $this;
    }

    public function code(int $code) 
    {
        $this->code = $code;
        return $this;
    }

    public function message(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function data(mixed $data)
    {
        $this->data = $data;
        return $this;
    }
}