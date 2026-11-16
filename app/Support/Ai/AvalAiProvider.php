<?php

declare(strict_types=1);

namespace App\Support\Ai;

use Illuminate\Http\Client\Factory;
use RuntimeException;

/** OpenAI-compatible chat completions; the model must answer JSON {output, proposals?}. */
final class AvalAiProvider implements AiProvider
{
    public function __construct(private readonly Factory $http, private readonly string $baseUrl, private readonly string $apiKey, private readonly string $model, private readonly int $timeout = 30) {}

    public function complete(array $request): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('AvalAI API key is not configured.');
        }
        $messages = [
            ['role' => 'system', 'content' => 'You are an ERP assistant for capability '.$request['module'].'.'.$request['capability'].'. Reply with JSON {"output": ..., "proposals": [{"type": ..., "payload": {...}}]}. Never claim that data was changed; changes are proposals for human approval.'],
            ['role' => 'user', 'content' => json_encode($request['input'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)],
        ];
        if ($request['tool_results'] !== []) {
            $messages[] = ['role' => 'assistant', 'content' => null, 'tool_calls' => array_map(fn ($r) => ['id' => $r['id'], 'type' => 'function', 'function' => ['name' => self::wire($r['name']), 'arguments' => json_encode((object) $r['arguments'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)]], $request['tool_results'])];
            foreach ($request['tool_results'] as $result) {
                $messages[] = ['role' => 'tool', 'tool_call_id' => $result['id'], 'content' => json_encode($result['result'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)];
            }
        }
        $payload = ['model' => $this->model, 'messages' => $messages, 'max_tokens' => $request['max_tokens'], 'response_format' => ['type' => 'json_object']];
        if ($request['tools'] !== []) {
            $payload['tools'] = array_map(fn ($tool) => ['type' => 'function', 'function' => ['name' => self::wire($tool['name']), 'description' => $tool['description'], 'parameters' => $tool['parameters']]], $request['tools']);
        }
        $data = $this->http->timeout($this->timeout)->withToken($this->apiKey)->post(rtrim($this->baseUrl, '/').'/chat/completions', $payload)->throw()->json();
        $message = is_array($data) ? ($data['choices'][0]['message'] ?? null) : null;
        if (! is_array($message)) {
            throw new RuntimeException('Invalid AvalAI response.');
        }
        $usage = ['input_tokens' => (int) ($data['usage']['prompt_tokens'] ?? 0), 'output_tokens' => (int) ($data['usage']['completion_tokens'] ?? 0)];
        $calls = [];
        foreach ($message['tool_calls'] ?? [] as $call) {
            $arguments = json_decode((string) ($call['function']['arguments'] ?? '{}'), true);
            $calls[] = ['id' => (string) ($call['id'] ?? ''), 'name' => str_replace('__', '.', (string) ($call['function']['name'] ?? '')), 'arguments' => is_array($arguments) ? $arguments : []];
        }
        if ($calls !== []) {
            return ['output' => null, 'tool_calls' => $calls, 'proposals' => [], 'usage' => $usage];
        }
        $content = json_decode((string) ($message['content'] ?? ''), true);
        if (! is_array($content) || ! array_key_exists('output', $content)) {
            throw new RuntimeException('AvalAI response is not the required JSON object.');
        }

        return ['output' => $content['output'], 'tool_calls' => [], 'proposals' => is_array($content['proposals'] ?? null) ? array_values($content['proposals']) : [], 'usage' => $usage];
    }

    private static function wire(string $tool): string
    {
        return str_replace('.', '__', $tool);
    }
}
