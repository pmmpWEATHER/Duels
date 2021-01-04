<?php

namespace Duels\task;

use pocketmine\scheduler\Task;
use pocketmine\level\sound\{PopSound,GenericSound};
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\utils\TextFormat as TE;
use pocketmine\utils\{Config,Color};
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\level\particle\DustParticle;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\block\Block;
use pocketmine\network\mcpe\protocol\MoveEntityAbsolutePacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\{AddEntityPacket,EntityEventPacket,PlaySoundPacket};
use Duels\manager\Settings;
use Duels\Duels;
use Duels\score\ScoreAPI;

class DuelsTask extends Task {
     public $prefix = Settings::GAME_PREFIX;
     public $plugin;
  
    public function __construct(Duels $plugin){

		$this->plugin = $plugin; }
    
    public function onRun(int $currentTick) : void {
		$config = new Config($this->plugin->getDataFolder() . "/config.yml", Config::YAML);
		$arenas = $config->get("arenas");                
		if(!empty($arenas)) {
			foreach($arenas as $arena) {
				$level = $this->plugin->getServer()->getLevelByName($arena);
				if($level instanceof Level) {
					  $players = $level->getPlayers();
					  $counttime = $this->plugin->manager->hasArenaCount($arena) ?? 0; 
					  $online = $this->plugin->manager->hasPlayers($arena)== is_null(null) ? $this->plugin->manager->hasPlayers($arena) : null;  
					  $resetM = $this->plugin->manager->hasArenaCount($arena) == null ? 0 : $this->plugin->manager->hasArenaCount($arena);
					  $new = new Config($this->plugin->getDataFolder() . "WC/".$arena.".yml",Config::YAML);
            $slots = $the->get("slots");
      //start a new match     
      if($this->plugin->isArenaUse($arena)==true) {
                     $slote = $slots+40; } else {
                         $slote = $this->plugin->startslot;
                         }
				if($counter>=$slote) { 
