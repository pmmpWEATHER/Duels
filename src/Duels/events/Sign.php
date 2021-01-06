<?php

namespace Duels\events;

use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\TextFormat as TE;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\entity\EntityFactory;
use pocketmine\level\Level;
use pocketmine\event\Listener;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\math\{Vector3,Vector2};
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use Duels\manager\Settings;
use Duels\Duels;

class Sign implements Listener {

    public $plugin;
  	public $version = 1.16.200;
  	public $main = "events";

	  public function __construct(Duels $plugin){
		  $this->plugin = $plugin;
			$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	  }
    
    public function onInteract(PlayerInteractEvent $event) { 
        $config = new Config($this->plugin->getDataFolder() . "config.yml", Config::YAML);
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $arena = $player->getLevel()->getFolderName();
        $tile = $player->getLevel()->getTile($block);
        if($tile instanceof Sign) {
        $text = $tile->getText();
           if($text[0]==Settings::SIGN_PREFIX_TOSTART || $text[0]==Settings::SIGN_PREFIX_TELEPORT || $text[0]==Settings::SIGN_PREFIX_START || $text[0]==Settings::SIGN_PREFIX_END) { 
             if($text[3] == TE::RED."Spectator") { 
             $map = str_replace([Settings::SIGN_MAP_1,Settings::SIGN_MAP_2,Settings::SIGN_MAP_3,Settings::SIGN_MAP_4],"",$text[2]);
             $lob = $this->plugin->getServer()->getLevelByName($mapa);
             $player->teleport($this->plugin->getServer()->getLevelByName($map)->getSafeSpawn(),0,0); 
             $player->getInventory()->clearAll();
             $player->removeAllEffects();
             $player->setGamemode(3);
 $this->plugin->manager->setPlayerSpec($player->getName(),$player->getLevel()->getFolderName());
 foreach($lob->getPlayers() as $playersinarena){  
$playersinarena->sendMessage(Settings::GAME_PREFIX.TE::WHITE." new (spectator) ".TE::GRAY.$player->getNameTag().TE::AQUA." He joined the game"); }

 } }

if($text[0]==Settings::SIGN_PREFIX_TOSTART || $text[0]==Settings::SIGN_PREFIX_TELEPORT || $text[0]==Settings::SIGN_PREFIX_START || $text[0]==Settings::SIGN_PREFIX_END) { 
if ($text[3] == TE::GREEN."Waiting") { 
 $mapa = str_replace([Settings::SIGN_MAP_1,Settings::SIGN_MAP_2,Settings::SIGN_MAP_3,Settings::SIGN_MAP_4],"",$text[2]);
 $lob = $this->plugin->getServer()->getLevelByName($mapa);
 $this->plugin->manager->setPlayer($player->getName(),$mapa);
 $levelArena = $this->plugin->getServer()->getLevelByName($mapa);
 $playersArena = $levelArena->getPlayers();
 $ac = new Config($this->plugin->getDataFolder() . "TG/$mapa.yml", Config::YAML);
 $slotlvl = $ac->get("slots");
 $player->setAllowFlight(false);  
 $player->setGamemode(2);
 $player->getInventory()->clearAll();
$player->getArmorInventory()->clearAll();
 $player->removeAllEffects();
 $onlin = $this->plugin->manager->hasArenaCount($mapa);
 $tpuch = $onlin == null ? 1 : $onlin;
 $stun = $config->get($mapa."Spawn".$tpuch);
 $player->teleport(new Position($stun["X"]+0.5, $stun["Y"]+1,$stun["Z"]+0.5,$levelArena),$stun["YAW"],$stun["PITCH"]);
 foreach($lob->getPlayers() as $playersinarena){  
$this->plugin->addSounds($playersinarena,"random.orb");
 $playersinarena->sendMessage("§8[§7JOIN-GAME§8]".TE::WHITE."» ".TE::GREEN.$player->getName().TE::GRAY." joined the game ".TE::WHITE."(".$onlin."/".$slotlvl.")"); }
 $player->setFood(20);
 $player->addTitle(TE::AQUA.TE::BOLD."JOINED",TE::WHITE."has entered the game",20,40,20);
 $player->setHealth(20);
 usleep(500000);
 $this->plugin->manager->loadCage($player);
 $this->plugin->addOnline($player->getName());
 } } } } 


	


	


	public function onInnteract(PlayerInteractEvent $event) {


		$player = $event->getPlayer();


		$block = $event->getBlock();


		$tile = $player->getLevel()->getTile($block);


		if($tile instanceof Sign) {


			if(isset($this->plugin->signOps[$player->getName()])) {


				$config = new Config($this->plugin->getDataFolder() . "TG/".$this->plugin->manager->namesign.".yml",Config::YAML);


				$slots = $config->get("slots"); 


				$tile->setText(Settings::SIGN_PREFIX_TOSTART,TE::WHITE."0 \ $slots",Settings::SIGN_MAP_1.$this->plugin->manager->namesign,TE::GREEN."Waiting");


				unset($this->plugin->signOps[$player->getName()]);


				$this->plugin->manager->namesign = "";


				$dire = $player->getDirection();


			    $x = $tile->getX(); $y = $tile->getY(); $z = $tile->getZ(); $xyz = array(intval($x), intval($y+0.1), intval($z),$dire);


				$config->set("slots",$slots);


				$config->set("Sign",$xyz);


				$config->save();


				$player->sendMessage(Settings::GAME_PREFIX."Cartel ha sido creado"); } } }


	


	


 


 }
