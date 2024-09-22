<?php

namespace Tests\Supports;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

trait TestingFormRequest
{
    /**
     * @beforeAll
     */
    protected function mockGate(): void
    {
        Gate::shouldReceive('authorize->allowed')->andReturnTrue();
    }

    protected function createRequest(array $parameters = [], User $user = null): Request
    {
        $collable = [$this->requestClass, 'create'];
        $args = ['', 'POST', $parameters];

        return call_user_func($collable, ...$args)
            ->setContainer($this->app)
            ->setRedirector($this->app[Redirector::class])
            ->setUserResolver(fn () => $user ?? User::factory()->create());
    }

    protected function createRequestAndValidate(array $parameters = [], array $bindings = [], User $user = null): Request
    {
        $request = $this->createRequest($parameters, $user);
        foreach ($bindings as $key => $value) {
            $request->{$key} = $value;
        }
        $request->validateResolved();

        return $request;
    }

    protected function assertValidationException(callable $try, callable $catch): void
    {
        try {
            $try();
            $this->assertTrue(false, 'バリデーションエラーが発生しませんでした');
        } catch (ValidationException $e) {
            $catch($e);
        }
    }
}
