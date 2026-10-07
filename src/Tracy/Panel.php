<?php

declare(strict_types=1);

namespace Skaut\SkautisNette\Tracy;

use Tracy\Dumper;
use Tracy\IBarPanel;

/**
 * Tracy bar panel listing the skautIS calls of the current request.
 */
class Panel implements IBarPanel
{
    public function __construct(private readonly QueryLog $log)
    {
    }

    public function getTab(): string
    {
        $queries = $this->log->getQueries();
        $failed = \count(array_filter($queries, static fn (Query $query): bool => $query->error !== null));

        return '<span title="skautIS">'
            .'<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="'.($failed > 0 ? '#d33' : '#555').'" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5v3l2 2"/></svg>'
            .'<span class="tracy-label">'.\count($queries).' skautIS'
            .($queries !== [] ? \sprintf(' / %.1f ms', $this->log->getTotalTime() * 1000) : '')
            .'</span></span>';
    }

    public function getPanel(): string
    {
        $queries = $this->log->getQueries();
        if ($queries === []) {
            return '';
        }

        $rows = '';
        foreach ($queries as $index => $query) {
            $rows .= '<tr'.($query->error !== null ? ' style="background:#fdd"' : '').'>'
                .'<td>'.\sprintf('%.1f', $query->duration * 1000).'</td>'
                .'<td><strong>'.htmlspecialchars($query->name).'</strong><br>'.Dumper::toHtml($query->args, [Dumper::COLLAPSE => true]).'</td>'
                .'<td>'.($query->error !== null ? '<pre>'.htmlspecialchars($query->error).'</pre>' : Dumper::toHtml($query->result, [Dumper::COLLAPSE => true])).'</td>'
                .'<td>'.$this->renderTrace($query, (string) $index).'</td>'
                .'</tr>';
        }

        return '<h1>skautIS: '.\count($queries).' calls</h1>'
            .'<div class="tracy-inner"><table><tr><th>ms</th><th>Method</th><th>Result</th><th>Trace</th></tr>'.$rows.'</table></div>';
    }

    private function renderTrace(Query $query, string $id): string
    {
        $lines = [];
        foreach ($query->trace as $frame) {
            $function = isset($frame['function']) && \is_string($frame['function']) ? $frame['function'] : '?';
            $class = isset($frame['class']) && \is_string($frame['class']) ? $frame['class'].'::' : '';
            $line = isset($frame['line']) && \is_scalar($frame['line']) ? ':'.(string) $frame['line'] : '';
            $lines[] = htmlspecialchars($class.$function.$line);
        }

        return '<a href="#tracy-skautis-'.$id.'" class="tracy-toggle tracy-collapsed">Trace</a>'
            .'<div id="tracy-skautis-'.$id.'" class="tracy-collapsed"><small>'.implode('<br>', $lines).'</small></div>';
    }
}
