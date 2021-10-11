<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NoteCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|AnonymousResourceCollection|\JsonSerializable
     */
    public function toArray($request)
    {
        return NoteResource::collection($this->collection);
    }

    public function pagination()
    {
        return [
            'current' => $this->currentPage(),
            'last' => $this->lastPage(),
            'base' => $this->url(1),
            'next' => $this->nextPageUrl(),
            'prev' => $this->previousPageUrl()
        ];
    }
}
