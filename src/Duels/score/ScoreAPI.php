<?php

namespace Duels\score;

use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\{RemoveObjectivePacket,SetDisplayObjectivePacket,SetScorePacket,types\ScorePacketEntry};
use pocketmine\{Player,Server,event\Listener,plugin\PluginBase,utils\Config};
use Duels\Duels; 

class ScoreAPI {
  private static $instance;
  private $scoreboards = [];
  public $plugin;

  public function __construct(UHCM $plugin) {
		$this->plugin = $plugin; 
  }

 
  public static function getInstance(): Scoreboard {
		return self::$instance;
	}

	public function new(Player $player, string $objectiveName, string $displayName): void {
		if(isset($this->scoreboards[$player->getName()])){
			$this->remove($player);
		}
		$play = new SetDisplayObjectivePacket();
		$play->displaySlot = "sidebar";
		$play->objectiveName = $objectiveName;
		$play->displayName = $displayName;
		$play->criteriaName = "dummy";
		$play->sortOrder = 0;
		$player->sendDataPacket($play);
		$this->scoreboards[$player->getName()] = $objectiveName;
	}


	public function remove(Player $player): void {

		if(isset($this->scoreboards[$player->getName()])){
		$objectiveName = $this->getObjectiveName($player);
		$play = new RemoveObjectivePacket();
		$play->objectiveName = $objectiveName;
		$player->sendDataPacket($play);
		unset($this->scoreboards[$player->getName()]);
	}

	}

	public function setLine(Player $player, int $score, string $message): void {
		if(!isset($this->scoreboards[$player->getName()])){
			$this->plugin->getLogger()->error("Cannot set a score to a player with no scoreboard");
			return;
    }

		$objectiveName = $this->getObjectiveName($player);
		$entry = new ScorePacketEntry();
		$entry->objectiveName = $objectiveName;
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;
		$play = new SetScorePacket();
		$play->type = $play::TYPE_CHANGE;
		$play->entries[] = $entry;
		$player->sendDataPacket($play);

	}


	public function getObjectiveName(Player $player): ?string {
		return isset($this->scoreboards[$player->getName()]) ? $this->scoreboards[$player->getName()] : null;
	}
  

}
