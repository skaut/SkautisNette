<?php

declare(strict_types=1);

namespace Skautis\Nette\Tracy;

use Nette;
use Skaut\Skautis\Wsdl\WsdlManager;
use Tracy;
use Tracy\Debugger;


/**
 * Skautis panel for Tracy.
 *
 * @author Hána František
 */
class Panel implements Tracy\IBarPanel
{
	use Nette\SmartObject;

    /** @var EventDispatcher */
    private $eventDispatcher;

    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
    }

    /**
	 * Registers event listener on WebService objects via WsdlManager
	 */
	public function register(WsdlManager $wsdlManager): void
	{
        $wsdlManager->setEventDispatcher($this->eventDispatcher);
		Debugger::getBar()->addPanel($this);
	}

	/**
	 * @inheritdoc
	 */
	public function getTab(): string
	{
		$totalTime = 0;
		foreach ($this->eventDispatcher->getQueries() as $query) {
			$totalTime += $query->time;
		}
		return '<span title="skautis"><img src="data:image/png;base64,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">'
		. count($this->eventDispatcher->getQueries()) . ' queries'
		. ($totalTime ? sprintf(' / %0.1f&nbsp;ms', $totalTime * 1000) : '')
		. '</span>';
	}


	/**
	 * @inheritdoc
	 */
	public function getPanel(): string
	{
		$cnt = 0;
		$s = "";
		foreach ($this->eventDispatcher->getQueries() as $query) {
			$rowId = "tracy-debug-Skautis-args-row-$cnt";
			$s .= "<tr>"
				. "<td>" . sprintf('%0.2f', $query->time * 1000) . "</td>"
				. "<td>{$query->fname}(" . $this->formatToggle('Args', $rowId) . ")<div id='$rowId' class='tracy-collapsed'>" . $this->dump(reset($query->args[0])) . "</div></td>"
				. "<td>" . $this->formatToggle('Result'). "<div class='tracy-collapsed'>" . $this->dump($query->result) . "</div></td>"
				. "<td>" . $this->prepareTrace($query->trace) . "</td>"
				. "</tr>";
			$cnt++;
		}

		return empty($this->eventDispatcher->getQueries()) ? '' :
			'<h1>Skautis</h1>'
			. '<div class="tracy-inner">'
			. '<table>'
			. '<tr><th>Time&nbsp;ms</th><th>Function&nbsp;name</th><th>Result</th><th>Trace</th></tr>'
			. $s
			. '</table>'
			. '</div>';
	}


	/**
	 * @param mixed[] $trace
	 * @return string
	 */
	protected function prepareTrace(array $trace): string
	{
		$s = "";
		$cnt = 0;
		foreach ($trace as $f) {
			$s .= "" . ++$cnt . ". " . $f['function'] . " (" . (array_key_exists("class", $f) ? ":" . $f['class'] : "") . (array_key_exists("line", $f) ? ":" . $f['line'] : "") . ")" . '<br>';
		}
		return $this->formatToggle('Trace') . "<div class='tracy-collapsed'>" . $s . "</div>";
	}


	/**
	 * @param mixed $object
	 * @return string
	 */
	protected function dump($object): string
	{
		return Debugger::dump($object, TRUE);
	}


	/**
	 * @param string $name text to display in toggle link
	 * @param string|NULL $rel id of toggleable element
	 * @return string
	 */
	protected function formatToggle(string $name, ?string $rel = NULL): string
	{
		return "<a href='#" . ($rel === NULL ? "" : "$rel' rel='#$rel") . "' class='tracy-toggle tracy-collapsed'>$name</a>";
	}

}
