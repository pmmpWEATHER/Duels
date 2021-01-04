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
					  $reset = $this->plugin->manager->hasArenaCount($arena) == null ? 0 : $this->plugin->manager->hasArenaCount($arena);
					  $new = new Config($this->plugin->getDataFolder() . "WC/".$arena.".yml",Config::YAML);
                                          $slots = $the->get("slots");
                                     //start a new match     
                                     if($this->plugin->isArenaUse($arena)==true) {
                                        $slot = $slots+40; } else {
                                        $slot = $this->plugin->startslot;
                                     }
				     if($counttime>=$slot) { 
				
		                     if(Settings::GAME_STATUS == $config->get($arena."Game")) {
				        $start = $config->get($arena."ToStartime");
				        $start--;
					$config->set($arena."ToStartime", $start);
                                        $config->save();
					     
				    foreach($players as $pl) {
				      // 10 seconds until game starting
                                      if($start>=11 && $start <= 40) {
                                          $this->runningTo($pl,$start,$arena);
                                      }
                    
                                      if($start>=1 && $start <= 10) {
                                      $time = $start>5 ? "§6".$start : "§c".$start;
                                      $this->plugin->addSounds($pl,"note.chime",$start);
                                      $pl->addTitle(" ","§l§eFightning in:\n ".$time,20,40,20);


                                     } if($start==0) {
                                     $this->plugin->score->remove($pl);
                                     $this->plugin->addSounds($pl,"random.levelup");

                            }

                        }
					     
                        if($resetM==0 || $resetM==null || count($players)==0 || (bool)$players == false) {

                         	foreach($players as $pl) { 
                                    if($this->plugin->isArenaUse($arena)==true) {
                                      $ares = new Config($this->plugin->getDataFolder()."DATA/MM".$arena.".yml", Config::YAML);
	                              $author = $ares->get("AUTHOR");
	                              $this->plugin->deleteCrasts($author);
	                            }
                       
			            $pl->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn(),0,0);
                                    $pl->setGameMode(2); }

                                    if($arena=="world") continue;


					    $this->plugin->manager->reloadMap($arena);


					    $this->plugin->manager->setBlockSign(5,$arena);


			$config->set($arena."Game",Settings::GAME_STATUS_DEFAULT);

                        $config->set($arena."ToStartime", Settings::TIME_TO_START_1);

                        $config->set($arena."TeleportTime", Settings::TIME_TELEPORT_2);
                        $config->set($arena."PlayTime", Settings::TIME_START_3);
                        $config->set($arena."EndTime", Settings::TIME_END_4);
