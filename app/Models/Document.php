<?php

namespace App\Models;

class Document implements JsonSerializable
{

    private $id;
    private $description;
    private $userId;
    private $documentURI;
    private $isValidated;
    private $notification;
    private $type;
    private $createdAt;
    private $upadatedAt;


    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'user_id' => $this->userId,
            'document_uri' => $this->documentURI,
            'is_validated' => $this->isValidated,
            'notification' => $this->notification,
            'type' => $this->type,
            'created_at' => $this->createdAt,
            'updated_at' => $this->upadatedAt
        ];


    }

}