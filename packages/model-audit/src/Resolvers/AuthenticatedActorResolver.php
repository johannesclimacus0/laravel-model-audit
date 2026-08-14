<?php

namespace Local\ModelAudit\Resolvers;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Database\Eloquent\Model;
use Local\ModelAudit\Contracts\ActorResolver;

class AuthenticatedActorResolver implements ActorResolver
{
    public function __construct(private AuthFactory $auth) {}

    public function resolve(): ?Model
    {
        $actor = $this->auth->guard()->user();

        return $actor instanceof Model ? $actor : null;
    }
}
