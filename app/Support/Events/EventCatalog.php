<?php

declare(strict_types=1);

namespace App\Support\Events;

use App\Support\Modules\ModuleCatalog;

final class EventCatalog
{
    public function markdown(ModuleCatalog $catalog): string
    {
        $events = [];
        foreach ($catalog->manifests() as $id => $manifest) {
            foreach (['emits' => 'emitters', 'listens' => 'listeners'] as $field => $side) {
                foreach ($manifest[$field] ?? [] as $event) {
                    $events[$event] ??= ['emitters' => [], 'listeners' => []];
                    $events[$event][$side][] = $id;
                }
            }
        }
        ksort($events, SORT_STRING);
        $lines = [
            '# Event catalog', '',
            'Generated from all module manifests, including disabled and partial modules. Declarations are not proof of emitted events, registered handlers or delivery guarantees. An undeclared emitter is an integration gap.', '',
            '| Event | Declared emitters | Declared listeners |',
            '|---|---|---|',
        ];
        foreach ($events as $event => $sides) {
            $columns = [];
            foreach (['emitters', 'listeners'] as $side) {
                sort($sides[$side], SORT_STRING);
                $columns[] = $sides[$side] === [] ? '—' : '`'.implode('`, `', $sides[$side]).'`';
            }
            $lines[] = '| `'.$event.'` | '.implode(' | ', $columns).' |';
        }

        return implode("\n", $lines)."\n";
    }
}
