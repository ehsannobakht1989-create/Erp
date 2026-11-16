<?php

declare(strict_types=1);

namespace App\Support\Ai;

interface AiProvider
{
    /**
     * @param  array{module:string, capability:string, input:array<string,mixed>, tools:list<array{name:string, description:string, parameters:array<string,mixed>}>, tool_results:list<array{id:string, name:string, arguments:array<string,mixed>, result:mixed}>, max_tokens:int, request_id:string}  $request
     * @return array{output:mixed, tool_calls:list<array{id:string, name:string, arguments:array<string,mixed>}>, proposals:list<array{type:string, payload:array<string,mixed>}>, usage:array{input_tokens:int, output_tokens:int}}
     */
    public function complete(array $request): array;
}
