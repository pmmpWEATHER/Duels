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
				
				    foreach($players as $pl) {
				      // 10 seconds until game starting
                                      if($start>=11 && $start <= 40) {
                                          $this->runningTo($pl,$start,$arena);
                                      }
                    
                                      if($start>=1 && $start <= 10) {
                                      $time = $start>5 ? "§6".$start : "§c".$start;
                                      $this->plugin->addSounds($pl,"note.chime",$start);
                                      $pl->addTitle(" ","§l§eFighting in:\n ".$time,20,40,20);


                                     } if($start==0) {
                                     $this->plugin->score->remove($pl);
                                     $this->plugin->addSounds($pl,"random.levelup");

                            }

                        }
					     
                        if($resetM==0 || $resetM==null || count($players)==0 || (bool)$players == false) {

                         	foreach($players as $pl) { 
                                    if($this->plugin->isArenaUse($arena)==true) {
                                      $ares = new Config($this->plugin->getDataFolder()."Maps/MM".$arena.".yml", Config::YAML);
	                              $author = $ares->get("AUTHOR");
	                              $this->plugin->deleteCrasts($author);
	                            }
                       
			            $pl->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn(),0,0);
                                    $pl->setGameMode(2); }
                                    if($arena=="world") continue;
			            $this->plugin->manager->reloadMap($arena);
		                    $this->plugin->manager->setBlockSign(5,$arena);
			            $config->set($arena."Game",Settings::GAME_STATUS_DEFAULT);
                                    $config->save(); } 
                                    //Set the time day as soon as match begins
                                    if($start<=0) {
	                              $level->setTime(Level::TIME_DAY);
	                              $level->stopTime();
			            $this->plugin->manager->setKiller($pl->getName());
                                    }
                                 $this->plugin->manager->setBlockSign(4,$arena);
                                 $config->set($arena."Game",Settings::PRE_TELEPORT_2);
                                 $config->save();

                                } } } else {

                  	          if(Settings::GAME_STATUS == $config->get($arena."Game")) {

                  	            foreach($players as $pl) {
					    // 1st player entering the match
                                      $title = $this->plugin->manager->setColorBoss($config->get($arena."Game"));
                                     $pl->sendTip($title." §rWaiting for a player to start:§a ".$counter."§f /§2 ".$slots);
                        
                  	            }

                  	          }
				}
				// Task for starting the match
                               if($resetM==0 || $resetM==null || count($players)==0 || (bool)$players == false) {
                         	   foreach($players as $pl) { 
                                      if($this->plugin->isArenaUse($arena)==true) {
                                          $ares = new Config($this->plugin->getDataFolder()."Maps/MM".$arena.".yml", Config::YAML);
	                                  $author = $ares->get("AUTHOR");
	                                  $this->plugin->deleteCrasts($author);
	                                  }
                                          $pl->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn(),0,0);
                                          $pl->setGameMode(2); }

                                         if($arena=="world") continue;
					    $this->plugin->manager->reloadMap($arena);
					    $this->plugin->manager->setBlockSign(5,$arena);
				            $config->set($arena."Game",Settings::GAME_STATUS_DEFAULT);
				            $config->save(); } 

                                            foreach($players as $pl) {
                                              if($start==1) {
                                                 $this->plugin->score->remove($pl);
                                                 $players2 = $this->plugin->manager->hasPlayers($arena)== is_null(null) ? $this->plugin->manager->hasPlayers($arena) : null;
                                                   if(isset($players2[$pl->getName()])) {
					              // Start + the top Duelers display
                                                      $pl->setGameMode(0);
                                                      $this->plugin->manager->getTopDuels($pl);
                                                      $this->plugin->removeItems($pl);
                                                   }
                                              }
						   
				              if($start<=0) { 
                                                foreach($players as $pl) {
                                                    $pl->setImmobile(false);
                                                    $pl->addTitle(" §l§aFIGHT","§l§dKill the other player!!",20,40,20);
                                                    $this->plugin->addSounds($pl,"mob.pillager.celebrate");
                                                    $pl->sendMessage($this->getPlugin()->translateString("game.started"));
						    $this->plugin->manager->setBlockSign(14,$arena);
						}
					      }
						    
					     if($start<=0) {
                                                 $this->plugin->manager->setBlockSign(14,$arena);
                                                 $config->set($arena."Game",Settings::PRE_START_3);
                                                 $config->save();
                                             } }
					 
					     if(Settings::PRE_START_3 == $config->get($arena."Game")) {
						$start = $config->get($arena."PlayTime");
						$start--;
						$config->set($arena."PlayTime", $start);
                                                $config->save();
					// If there is one person left in the arena     
				              if($counter<=1) { 
                                                 foreach($players as $player) { 
                                                    if(isset($onlines[$player->getName()])) {
                                                         $player->addTitle("§l§6VICTORY","§r§7You were the last man standing",20,40,20);
                                                         $this->plugin->addSounds($player,"mob.cat.beg");
			                                 $this->plugin->getServer()->broadcastMessage("§8» §f===§c§lDUELS§r==="); 
							 $this->plugin->getServer()->broadcastMessage("§6Winner §8#1 §f".$player->getName());
                                                         $this->plugin->getServer()->broadcastMessage("§6Won the game in:§a ".$arena);
                                                         $this->plugin->getServer()->broadcastMessage("§8» §f==============="); }
                                                         $player->setGamemode(Player::ADVENTURE);
                                                         $player->setHealth(20);
                                                         $player->setFood(20);
                                                         $config->set($arena."Game",Settings::PRE_END_4);
			                                 $config->save();
			                                 $this->plugin->manager->setBlockSign(10,$arena);
                                                   }

                                                }
						// end of game and teleporting players to spawn    
						if($resetM==0 || $resetM==null || count($players)==0 || (bool)$players == false) {
                                                     if($this->plugin->isArenaUse($arena)==true) {
                                                          $ares = new Config($this->plugin->getDataFolder()."DATA/MM".$arena.".yml", Config::YAML);
	                                                  $author = $ares->get("AUTHOR");
	                                                  $this->plugin->deleteCrasts($author);
	                                             }
                         	                     foreach($players as $pl) { 
                                                         $pl->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn(),0,0);
                                                         $pl->setGameMode(2); }
                                                       if($arena=="world") continue;
					                   $this->plugin->manager->reloadMap($arena);
					                   $this->plugin->manager->setBlockSign(5,$arena);
						           $config->set($arena."Game",Settings::GAME_STATUS_DEFAULT);
					                   $config->save(); } 
						       // Small Scoreboard and Game Task
	              				       if($start==30*10 ) { 
                                                         foreach($players as $pl) {
                                                            $api->new($pl, $pl->getName(), TE::BOLD.TE::GOLD."DUELS");
                                                            $i = 0;
                                                            $lines = [
                                                            TE::RED."   ",
                                                            TE::BOLD.TE::GOLD."Time Left: ".TE::WHITE.$start,
                                                            TE::BLUE."   ",
                                                            TE::BOLD.TE::GOLD."Your Ping: ".TE::WHITE.$pl->getPing(),
                                                            TE::WHITE."   ",
                                                            TE::BOLD.TE::GOLD."Your Map: ".TE::WHITE.$arena,
                                                            TE::GOLD."    ",
                                                            TE::BOLD.TE::GOLD."Your Health: ".TE::WHITE.$pl->getHealth(),
                                                            ];
                                                         }
                                                       }
						      // If nobody fought then the time is ending.
						       if($start<=0) {
                                                          $this->plugin->manager->setBlockSign(10,$arena);
                                                          $config->set($arena."Game",Settings::PRE_END_4);
                                                          $config->save();
                                                       } }
                                                      foreach($players as $pl) {
                                                         if(isset($onlines[$pl->getName()])) {
                                                            if($start>=2 && $start<=8) {
                                                            }
                                                         }
                        	                         $pl->sendTip("§aRestarting in:§f ".$start);
                                                      }
			
			                              if($start<=0) {
                        	                         foreach($players as $pl) {                      
                                                           $this->plugin->manager->delPlayer($pl->getName(),$pl->getLevel()->getFolderName());
                                                           $pl->setGameMode(2); 
                                                           $pl->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn(),0,0);
                                                        }
                                                        $this->plugin->manager->setBlockSign(5,$arena);
                                                        $config->set($arena."Game",Settings::GAME_STATUS_DEFAULT);
                                                        $config->save();
                                                        if($arena=="world") continue;
                                                           if($this->plugin->isArenaUse($arena)==true) {
                                                              $ares = new Config($this->plugin->getDataFolder()."Maps/MM".$arena.".yml", Config::YAML);
	                                                      $author = $ares->get("AUTHOR");
	                                                      $this->plugin->deleteCrasts($author);
	                                                   }
					                  $this->plugin->manager->reloadMap($arena);
                                                       } }
					} } } }


					


}
			
			 			        

					

						    
				
							
						


                        
