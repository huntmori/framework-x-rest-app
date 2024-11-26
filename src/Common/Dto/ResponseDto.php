<?php
namespace Damoyo\Api\Common\Dto;

use JsonSerializable;

class ResponseDto implements JsonSerializable {

    public int $code {
        get => $this->code;
        set => $this->code = $value;
    }
    
    public bool $result {
        get => $this->result;
        set => $this->result = $value;
    }
    
    public string $message = '' {
        get => $this->message;
        set => $this->message = $value;
    }
    
    public mixed $data {
        get => $this->data;
        set => $this->data = $value;
    }

    public function jsonSerialize(): mixed {
        return get_object_vars($this);
    }

    public static function toResponse(ResponseDto $data) 
    {
        return \React\Http\Message\Response::json($data);
    }
}