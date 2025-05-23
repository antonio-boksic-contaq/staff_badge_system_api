<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {

        // FARE CHECK SU PERMESSI 
        // ruolo trova utente giusto perchè uso ->user
        // permessi mi sa che trovano sempre ultimo utente dato che usano ->user()

        //dd($request->user->getRoleNames());
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email'=> $this->email,
            'gender'=> $this->gender,
            //'team_id'=> $this->team_id,
            //'team' => new TeamResource($this->team),
            //'projects' => ProjectResource::collection($this->whenLoaded('projects')),
            'role' => $this->getRoleNames()->first(),
            //'role' => $this->roles()->first()->name,
            //'permissions' => Role::findByName($this->roles->first()->name, 'api')->permissions()->where('software','hr')->pluck('name'),
            //'permissions' => Role::findByName($this->roles->first()->name)->permissions()->pluck('name'),
            //'permissions' => $request->user()->getPermissionsViaRoles()->pluck('name'),
            //'deleted_at' => $this->deleted_at,
            // questo mi serve per passare il LabelField al v-select nel frontend per poter vedere nome e cognome nella select.
            "complete_name" => $this->lastname . " " . $this->name
        ];
    }
}