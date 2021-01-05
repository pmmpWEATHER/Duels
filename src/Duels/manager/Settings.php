<?php

namespace Duels\manager;

use pocketmine\utils\TextFormat as TE;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\Player;
use Duels\Duels;
use Duels\manager\{DropTrailt};
use pocketmine\tile\Sign;
use pocketmine\block\Block;
use pocketmine\math\{Vector3,Vector2};
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\inventory\ChestInventory;
use pocketmine\network\mcpe\protocol\{AddEntityPacket,EntityEventPacket,PlaySoundPacket};
use pocketmine\tile\Chest;
use pocketmine\item\ItemFactory;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;

class Settings {

  use DropTrailt;

	const GAME_PREFIX = TE::WHITE."[".TE::RED."DUELS".TE::WHITE."]";
	const GAME_JOIN = TE::WHITE."[".TE::AQUA."NEW".TE::WHITE."]";
	const GAME_STATUS = 1;
	const GAME_STATUS_DEFAULT = 1;
  
  const TIME_TO_START_1 = 10; //When second player joins the match
	const TIME_TELEPORT_2 = 5; // Title countdown
	const TIME_START_3 = 30*10; // 5 Mintues gametime
	const TIME_END_4 = 11; //return to hub time
	const PRE_TO_START_1 = 1;
	const PRE_TELEPORT_2 = 2;
	const PRE_START_3 = 3;
	const PRE_END_4 = 4;
  
  public $players = [];
	public $spectators = [];
	public $cages = [];
	public $slots = [];
	public $myskins = [];
	public $mykits = [];
	public $myprojects = [];
  // Color for sign so that changes when an event occur.
	const SIGN_PREFIX = TE::BOLD.TE::RED."DUELS";
	const SIGN_COLOR1 = TE::BOLD;
	const SIGN_COLOR2 = TE::DARK_AQUA;
	const SIGN_PREFIX_TOSTART = TE::BOLD.TE::DARK_GREEN."DUELS";
	const SIGN_PREFIX_TELEPORT = TE::BOLD.TE::GOLD."DUELS";
	const SIGN_PREFIX_START = TE::BOLD.TE::RED."DUELS";
  const SIGN_PREFIX_END = TE::BOLD.TE::LIGHT_PURPLE."Uhc";
  
	const BOSS_COLOR_1 = TE::BOLD.TE::YELLOW."S6DUELS§7>>";
  const SIGN_MAP_1 = TE::DARK_GREEN;
	const SIGN_MAP_2 = TE::GOLD;
	const SIGN_MAP_3 = TE::DARK_RED;
	const SIGN_MAP_4 = TE::DARK_PURPLE;
  
  const FIGURE_COLOR = ["§8","§f","§7"];
	const SLOT_TOP = "Available";
	const SLOT_TOP_KILLS = "not Available ";
	const FIGURE = "☆";
	const WIN_NOT = "§l§cNO TOPS FOR THE SECOND";

	public static $TITLE_TOPS = "";

  public $plugin;
	public $kills = [];
	public $kits = [];
	public $dataspect = [];
	public $changenick = true;
	public $namesign = "Offined";
  
  public function __construct(Duels $plugin){
		    $this->plugin = $plugin;
	}
  
  public function getPlayers(string $arena,string $name = null) {
				return $this->hasArenaCount($arena);
	}
  
  public function setPlayer(string $name,string $arena,int $type = 0) {
		if(!isset($this->players[$arena][$name])) {
		$this->players[$arena][$name] = $name;
		$this->addSpawn($name,$arena);
		 }      
	}
	
	public function addSpawn(string $name,string $arena) {
		if(isset($this->slots[$arena])!=false){
		    for($q=1; $q<=count($this->slots[$arena])+1; $q++) {
	          $s = array_values($this->slots[$arena]);
	          if(!in_array($q,$s)) {
		       $this->slots[$arena][$name] = $q;
		        break;
		         }
	        }
		 } else {
			$this->slots[$arena][$name] = 1;
			}
	}
  
  public function deleteSpawn(string $name,string $arena) {
		if(isset($this->slots[$arena][$name])) {
		   unset($this->slots[$arena][$name]);
		 }      
	}
  
  public function getSpawn(string $name,string $arena) : int {
		if(isset($this->slots[$arena][$name])) {
		   return $this->slots[$arena][$name] + 1;
		 } else {
			return 1;
			}
	}
  
  public function setPlayerSpec(string $name,string $arena,int $type = 0) {
		if(isset($this->spectators[$arena][$name])) {
		   		return "This player exists";
		   	} else {
	      	$this->spectators[$arena][$name] = $name;
		 }      
	}
  
  public function delPlayer(string $name,string $arena,int $type = 0) {
		if(isset($this->players[$arena][$name])) {
			 $pl = $this->plugin->getServer()->getPlayer($name);
			 $pl->setAllowFlight(false);  
       $pl->getInventory()->clearAll();
       $pl->setImmobile(false);
       $pl->setGamemode(Player::ADVENTURE); 
       $this->delKill($pl->getName());
       $this->delKit($pl->getName());
       this->deleteSpawn($name,$arena);
			 unset($this->players[$arena][$name]);
		}
	}
  
  public function delFake(string $name,string $arena,int $type = 0) {
		if(isset($this->players[$arena][$name])) {
			    unset($this->players[$arena][$name]);
		   	}
	}
  
  public function delFakeSpec(string $name,string $arena,int $type = 0) {
		if(isset($this->spectators[$arena][$name])) {
			    $pl = $this->plugin->getServer()->getPlayer($name);
			    $pl->setAllowFlight(false);  
          $Rpl->getInventory()->clearAll();
          $pl->setGamemode(Player::ADVENTURE); 
			    unset($this->spectators[$arena][$name]);
  	}
	}
  
  public function hasArenaCount(string $arena,string $name = null) {
		if(isset($this->players[$arena])) {
	   	$d = count($this->players[$arena]);
	          return $d;
	  } 
	}

  public function loadTops(int $top,int $break,string $data = "Wins") : string {
    $tops = new Config($this->plugin->getDataFolder()."/".$data.".yml", Config::YAML);
    if($tops->getAll()!=null){
       $all = $tops->getAll();
       $tt = 1;
       arsort($all);
       $p = []; 
       foreach($all as $users => $tops){
           if($tt==$top) { $p[$users] = $tops; } 
               $tt++; if($tt==$break) break; }
               $maxp = $p == null ? 0 : max($p);
               $topp = array_search($maxp, $p) == null ? TE::RED."lugar disponible" : array_search($maxp, $p);
               return TE::GOLD.$topp.TE::WHITE.": ".TE::YELLOW.$maxp;
	                  } else {
	               	return "No tops";
       }
  }
  
  public function getTopDuels(Player $pl) : void {
    $pl->sendMessage(TE::GREEN.TE::BOLD."========================");
    $pl->sendMessage(TE::RED.TE::BOLD."          DUELS    ");
    $pl->sendMessage(TE::AQUA.TE::BOLD." [☆1] ".$this->loadTops(1,2));
    $pl->sendMessage(TE::DARK_AQUA.TE::BOLD." [☆2] ".$this->loadTops(2,3));
    $pl->sendMessage(TE::RED.TE::BOLD." [☆3] ".$this->loadTops(3,4));
    $pl->sendMessage(TE::DARK_RED.TE::BOLD." [☆4] ".$this->loadTops(4,5));
    $pl->sendMessage(TE::DARK_RED.TE::BOLD." [☆5] ".$this->loadTops(5,6));
    $pl->sendMessage(TE::GREEN.TE::BOLD."========================");
    }
