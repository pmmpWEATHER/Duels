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
	
  public function hasPlayers(string $arena,string $name = null) {
      if(isset($this->players[$arena])) {
		$d = $this->players[$arena];
	          return $d;
      } 
  }
	
  public function hasArenaCountSpec(string $arena,string $name = null) {
     if(isset($this->spectators[$arena])) {
	$d = count($this->spectators[$arena]);
	return $d;
     } 
  }
	
  public function hasPlayersSpec(string $arena,string $name = null) {
     if(isset($this->spectators[$arena])) {
	$d = $this->spectators[$arena];
	return $d;
     } 

  }
	
  public function getArenas() {
     $arena = $this->plugin->arenas;
	return $arena;


  }
	
  public function kickGame(string $name,string $arena = "Speed") {
    if(isset($this->players[$arena][$name])) {
       $pl = $this->plugin->getServer()->getPlayer($name);
	 if($pl instanceof Player) {
            if($pl->isOnline()){
                $pl->getInventory()->clearAll();              
                $this->delKill($pl->getName());  
                $pl->setImmobile(false);
                $pl->setGamemode(3); 
                } 
	     }
	    unset($this->players[$arena][$name]);
	   } 
   }
	
   public function setKiller(string $name) {
	$this->kills[$name] = 0;
   }

   public function addKill(string $name) {
	$this->setKill($name);
	if(isset($this->kills[$name])) {
	$this->kills[$name] += 1;
	} else {
	$this->kills[$name] = 1;
	}
  }
	
  public function getKill(string $name) : int {
      if(isset($this->kills[$name])) {
	 $e = $this->kills[$name];
	  return $e;
	  } else {
	   return 0;
      }
  }
	
  public function delKill(string $name) {
     if(isset($this->kills[$name])) {
	unset($this->kills[$name]);
     }

  }

  public function getWins(string $name) {
      $tops = new Config($this->plugin->getDataFolder() . "/Wins.yml", Config::YAML);
      $get = $tops->get($name) == null ? 0 : $tops->get($name);
      return $get;
  }

  public function setWins(string $name) {
      $tops = new Config($this->plugin->getDataFolder() . "/Wins.yml", Config::YAML);
      $tops->set($name,$tops->get($name) + 1);
      $tops->save();
  }

  public function setKill(string $name) {
     $tops = new Config($this->plugin->getDataFolder() . "/Kills.yml", Config::YAML);
     $tops->set($name,$tops->get($name) + 1);
     $tops->save();
  }
	
  public function getKills(string $name) {
      $tops = new Config($this->plugin->getDataFolder() . "/Kills.yml", Config::YAML);
      $c = $tops->get($name) == null ? "§cNoData" : $tops->get($name);
      return $c;
  }

  public function getLost(string $name) {
	$lost = new Config($this->plugin->getDataFolder() . "/Lost.yml", Config::YAML);
	$get = $lost->get($name) == null ? "§cSinDatos" : $lost->get($name);
        return $get;
  }

  public function setLost(string $name) {
	$lost = new Config($this->plugin->getDataFolder() . "/Lost.yml", Config::YAML);
	$lost->set($name,$lost->get($name) + 1);
	$lost->save();
  }
	
  public function setBlockSign(int $color = 3,string $world) : void {
   	$level = $this->plugin->getServer()->getDefaultLevel();
	$tiles = $level->getTiles();
	foreach($tiles as $t) {
	   if($t instanceof Sign) {	
	      $text = $t->getText();
		if($text[0]==self::SIGN_PREFIX_TOSTART || $text[0]==self::SIGN_PREFIX_TELEPORT || $text[0]==self::SIGN_PREFIX_START || $text[0]==self::SIGN_PREFIX_END) {
		   $map = str_replace([self::SIGN_MAP_1,self::SIGN_MAP_2,self::SIGN_MAP_3,self::SIGN_MAP_4],"",$text[2]);
		   if($map==$world) {
		      $bll = new Config($this->plugin->getDataFolder() . "Maps/$mapa.yml", Config::YAML);
                      $sign = $bll->get("Sign");
                      $ups = $dire[3]==null ? 0 : $sign[3];
		      if($ups==0) {
                         $t->getLevel()->setBlock($t->add(1,0,0), Block::get(241,$color), false,false);
                         } else if($ups==1) {
                         $t->getLevel()->setBlock($t->add(0,0,1), Block::get(241,$color), false,false);
                         } else if($ups==2) {
                         $t->getLevel()->setBlock($t->add(-1,0,0), Block::get(241,$color), false,false);
                         } else if($ups==3) {
                         $t->getLevel()->setBlock($t->add(0,0,-1), Block::get(241,$color), false,false);
                      } 

       }    }

      } }
  }
  
  public function setWorldGame() {
      $config = new Config($this->plugin->getDataFolder()."/config.yml", Config::YAML);
      if($config->get("arenas")!=null) { 
	 $this->plugin->arenas = $config->get("arenas");
         $this->plugin->getServer()->getLogger()->notice("§l§bAll maps are full!!"); 
         } else { 
	      $this->plugin->getServer()->getLogger()->notice("§l§cNo more maps available!"); }
	      foreach($this->plugin->arenas as $name) { 
		if($name=="world") continue;
                   $this->reloadMap($name);
                   $this->setBlockSign(5,$name);
                   $config->set($name."Game",self::GAME_STATUS_DEFAULT);
                   $config->set($name. "ToStartime", self::TIME_TO_START_1);
                   $config->set($name. "TeleportTime", self::TIME_TELEPORT_2);
                   $config->set($name. "PlayTime", self::TIME_START_3);
                   $config->set($name."EndTime", self::TIME_END_4);
		   $config->save();
                }
		$config->save();
  }
	
  public function reloadMap($lev) {
      if ($this->plugin->getServer()->isLevelLoaded($lev)) {
           $this->plugin->getServer()->unloadLevel($this->plugin->getServer()->getLevelByName($lev)); }
           $zip = new \ZipArchive;
           $zip->open($this->plugin->getDataFolder() . 'arenas/' . $lev . '.zip');
           $zip->extractTo($this->plugin->getServer()->getDataPath() . 'worlds');
           $zip->close();
           unset($zip);
           $rgb = ["§b","§3","§e"];
           $this->plugin->getServer()->getLogger()->notice("§l§bCargando mapas§f: ".$rgb[mt_rand(0,2)].$lev);
           $this->plugin->getServer()->loadLevel($lev);
	  return true;
  }
	
  public function setTime(int $value) {
      $v = $value;
      $reddi = $v % 60;
      $jacket = ($v - $reddi) / 60;
      $valup = $jacket % 60;
      $s = str_pad($reddi, 2, "0", STR_PAD_LEFT);
      $m = str_pad($valup, 2, "0", STR_PAD_LEFT);
      return TE::WHITE.$m." §8:§f ".$s;	
  }
	
  public function setSegs(int $value) {
      $v = $value;
      $reddi = $v % 60;
      $jacket = ($v - $reddi) / 60;
      $valup = $jacket % 60;
      $s = str_pad($reddi, 2, "0", STR_PAD_LEFT);
      $m = str_pad($valup, 2, "0", STR_PAD_LEFT);
      return $s;	
  }
	
  public function setColorBoss(int $game = 0) : string {
      switch($game) {
	  case self::PRE_TO_START_1:
	  return self::BOSS_COLOR_1;
	  break;
	  case self::PRE_TELEPORT_2:
	  return self::BOSS_COLOR_2;
	  break;
	  case self::PRE_START_3:
	  return self::BOSS_COLOR_3;
	  break;
	  case self::PRE_END_4:
	  return self::BOSS_COLOR_4;
	  break;
          default:
	  return self::BOSS_COLOR_1;
	  break;	
      }
  }
  // The code where I always screw up so CHECK HERE ALWAYS WEATHER	
  public function getTopsKills() {
      $tops = new Config($this->plugin->getDataFolder().'/Wins.yml', Config::YAML);
      if($tops->getAll()!=null){
          $all = $tops->getAll();
          $tt = 1;
          arsort($all);
          $p = []; $s = []; $t = []; $c = []; $ci = [];
          foreach($all as $users => $tops){
               if($tt==1) { $p[$users] = $tops; }  
               if($tt==2) { $s[$users] = $tops; }
               if($tt==3) { $t[$users] = $tops; }
               if($tt==4) { $c[$users] = $tops; }
               if($tt==5) { $ci[$users] = $tops; } $tt++; if($tt==6) break; }
               $maxp = $p == null ? 0 : max($p);
               $topp = array_search($maxp, $p) == null ? TE::RED.self::SLOT_TOP : array_search($maxp, $p);
               $maxs = $s == null ? 0 : max($s);
               $tops = array_search($maxs, $s) == null ? TE::RED.self::SLOT_TOP : array_search($maxs, $s);
               $maxt = $t == null ? 0 : max($t);
               $topt= array_search($maxt, $t) == null ? TE::RED.self::SLOT_TOP : array_search($maxt, $t);
               $maxc = $c == null ? 0 : max($c);
               $topc = array_search($maxc, $c) == null ? TE::RED.self::SLOT_TOP : array_search($maxc, $c);
               $maxci = $ci == null ? 0 : max($ci);
               $topci = array_search($maxci, $ci) == null ? TE::RED.self::SLOT_TOP : array_search($maxci, $ci);
               return self::TITLE_TOPS."\n".
               TE::WHITE."[".TE::GRAY."#1".TE::WHITE."] ".TE::AQUA.$topp.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxp."\n".
               TE::WHITE."[".TE::GRAY."#2".TE::WHITE."] ".TE::AQUA.$tops.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxs."\n".
               TE::WHITE."[".TE::GRAY."#3".TE::WHITE."] ".TE::AQUA.$topt.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxt."\n".
               TE::WHITE."[".TE::GRAY."#4".TE::WHITE."] ".TE::AQUA.$topc.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxc."\n".
               TE::WHITE."[".TE::GRAY."#5".TE::WHITE."] ".TE::AQUA.$topci.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxci."\n";       
	} else {
		return self::WIN_NOT;
      }
  }
  
  public function getTopsDuels(bool $nice = true) {
	   if(true==$nice) {
	     self::$TITLE_TOPS = "§f§l|§7|§f|§7|§7|§aDuels§f|§7|§f|§7|§f|\n§l§3Leaderboard Game §aWins";
             $tops = new Config($this->plugin->getDataFolder().'/Wins.yml', Config::YAML);
             } else if(false==$nice) {
            self::$TITLE_TOPS = "§f§l|§7|§f|§7|§7|§bSkyWars§f|§7|§f|§7|§f|\n§l§3Leaderboard Game §bkills";
            $tops = new Config($this->plugin->getDataFolder().'/Kills.yml', Config::YAML);
	    }
            if($tops->getAll()!=null){
            $all = $tops->getAll();
              $tt = 1;
              arsort($all);
              $p = []; $s = []; $t = []; $c = []; $ci = []; $ce = []; $cie = []; $och = [];
              foreach($all as $users => $tops){
              if($tt==1) { $p[$users] = $tops; }  
              if($tt==2) { $s[$users] = $tops; }
              if($tt==3) { $t[$users] = $tops; }
              if($tt==4) { $c[$users] = $tops; }
              if($tt==5) { $ci[$users] = $tops; }
              if($tt==6) { $ce[$users] = $tops; }
              if($tt==7) { $cie[$users] = $tops; }
              if($tt==8) { $och[$users] = $tops; } $tt++; if($tt==9) break; }
              $maxp = $p == null ? 0 : max($p);
              $topp = array_search($maxp, $p) == null ? TE::RED.self::SLOT_TOP : array_search($maxp, $p);
              $maxs = $s == null ? 0 : max($s);
              $tops = array_search($maxs, $s) == null ? TE::RED.self::SLOT_TOP : array_search($maxs, $s);
              $maxt = $t == null ? 0 : max($t);
              $topt= array_search($maxt, $t) == null ? TE::RED.self::SLOT_TOP : array_search($maxt, $t);
              $maxc = $c == null ? 0 : max($c);
              $topc = array_search($maxc, $c) == null ? TE::RED.self::SLOT_TOP : array_search($maxc, $c);
              $maxci = $ci == null ? 0 : max($ci);
              $topci = array_search($maxci, $ci) == null ? TE::RED.self::SLOT_TOP : array_search($maxci, $ci);
              $maxce = $ce == null ? 0 : max($ce); 
              $topce = array_search($maxce, $ce) == null ? TE::RED.self::SLOT_TOP : array_search($maxce, $ce);
              $maxcie = $cie == null ? 0 : max($cie);
              $topcie = array_search($maxcie, $cie) == null ? TE::RED.self::SLOT_TOP : array_search($maxcie, $cie);
              $maxoch = $och == null ? 0 : max($och);
              $topoch = array_search($maxoch, $och) == null ? TE::RED.self::SLOT_TOP : array_search($maxoch, $och);
              return self::$TITLE_TOPS."\n".
              TE::WHITE."[".TE::AQUA."#1".TE::WHITE."] ".TE::AQUA.$topp.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxp."\n".
              TE::WHITE."[".TE::GOLD."#2".TE::WHITE."] ".TE::AQUA.$tops.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxs."\n".
              TE::WHITE."[".TE::GOLD."#3".TE::WHITE."] ".TE::AQUA.$topt.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxt."\n".
              TE::WHITE."[".TE::GOLD."#4".TE::WHITE."] ".TE::AQUA.$topc.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxc."\n".
              TE::WHITE."[".TE::RED."#5".TE::WHITE."] ".TE::AQUA.$topci.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxci."\n".
              TE::WHITE."[".TE::RED."#6".TE::WHITE."] ".TE::AQUA.$topce.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxce."\n".
              TE::WHITE."[".TE::RED."#7".TE::WHITE."] ".TE::AQUA.$topoch.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxcie."\n".
              TE::WHITE."[".TE::RED."#8".TE::WHITE."] ".TE::AQUA.$topoch.self::FIGURE_COLOR[mt_rand(0,2)]." .----- ".self::FIGURE.TE::GREEN.$maxoch."\n";       
	   } else {
		return self::WIN_NOT;
		}                     
  }	
}

	

	
  


	


	


