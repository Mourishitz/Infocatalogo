<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        if(!$user){
            $token = $request->header('Authorization');

            if (!$token && $request->has('token')) {
                $token = $request->input('token');
            }
            if ($token) {
                $user = Auth::guard('sanctum')->user();
            }
        }

        $isLiked = (bool)sizeof($this->likes()->where('owner_id', '=', $user->id ?? 0)->get());

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'liked' => $isLiked,
            'likes' => $this->likes->count(),
            'comments' => $this->comments->count(),
        ];
    }
}
