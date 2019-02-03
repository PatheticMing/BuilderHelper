<?php

namespace BuilderHelper;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\TextFormat as C;

class builderhelper extends PluginBase implements Listener {
    
    private $bh = C::AQUA . "[BuilderHelper] " . C::RESET;
    private $pcount;
    private $count;
    private $id;
    private $checkid;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->id = NULL;
        $this->getLogger()->info(C::GOLD . "Loaded!");
    }

    public function blockPlaceCount(BlockPlaceEvent $event) {
        $player = $event->getPlayer();
        if($this->checkid) {
			$this->id = $event->getBlock()->getId();
			$player->sendMessage($this->bh . C::GREEN . "Counting " . $this->pcount . " blocks!");
			$event->setCancelled(true);
           }
        if(isset($this->pcount) && isset($this->id)) {
            if($this->checkid) {
				$this->checkid = false;
			} else {
				$id = $event->getBlock()->getId();
				if($id !== $this->id) {
					$player->sendTip(C::YELLOW . "You placed " . C::LIGHT_PURPLE . $this->count . C::YELLOW . " / " . C::LIGHT_PURPLE . $this->pcount . C::YELLOW . "blocks!" );
				} else {
					$this->count++;
					$player->sendTip(C::YELLOW . "You placed " . C::LIGHT_PURPLE . $this->count . C::YELLOW . " / " . C::LIGHT_PURPLE . $this->pcount . C::YELLOW . "blocks!" );
				}
				if($this->count >= $this->pcount) {
					$player->sendMessage($this->bh. C:: GOLD . "You have placed " . $this->count . " blocks!");
					$this->count = NULL;
					unset($this->pcount);
					unset($this->id);
				}
			}
		}
    }
    
    public function blockBreakCount(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if(isset($this->pcount) && isset($this->id)) {
            $id = $event->getBlock()->getId();
			if($id === $this->id) {
				if($this->count <= 0) {
					$this->count = 0;
					$player->sendTip(C::YELLOW . "You placed " . C::LIGHT_PURPLE . $this->count . C::YELLOW . " / " . C::LIGHT_PURPLE . $this->pcount . C::YELLOW . "blocks!" );
				} else {
					$this->count--;
					$player->sendTip(C::YELLOW . "You placed " . C::LIGHT_PURPLE . $this->count . C::YELLOW . " / " . C::LIGHT_PURPLE . $this->pcount . C::YELLOW . "blocks!" );
				}
			} else {
				$player->sendTip(C::YELLOW . "You placed " . C::LIGHT_PURPLE . $this->count . C::YELLOW . " / " . C::LIGHT_PURPLE . $this->pcount . C::YELLOW . "blocks!" );
			}
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) :bool {
        if ($sender instanceof Player) {
            if(strtolower($command->getName() == "bh") && isset($args[0])) {
                switch($args[0]) {
                    case "pos":
                        $x = intval($sender->getX());
                        $y = intval($sender->getY());
                        $z = intval($sender->getZ());
                        $sender->sendMessage($this->bh . C::AQUA . "You are at" . C::RED . $x . C::AQUA . "," . C::RED . $y .  C:: AQUA . "," . C::RED .  $z);
                        break;
                    case "placed":
                        if(isset($this->count)) {
                        $sender->sendMessage($this->bh . C::GOLD . "You placed " . $this->count . "blocks!");
                        } else {
                            $sender->sendMessage($this->bh . C::RED . "You haven't start to count yet!");
                        }
                        break;
                    case "start":
						if(count($args) < 2) {
								$sender->sendMessage($this->bh . C::RED . "Please enter how many blocks you want to place!");
						} else {
							if(!ctype_digit($args[1])){
								$sender->sendMessage($this->bh . C::RED . "Please enter a positive integer! ");
							} elseif($args[1] <= 0) {
								$sender->sendMessage($this->bh . C::RED . "You won't need me if you are trying to place " . $args[1] . " block, right?");
							} else{
								$this->pcount = $args[1];
								$this->checkid = true;
								$sender->sendMessage($this->bh . C::GOLD . "Place down the block to let me know the block id!");
							}
						}
                        break;
					case "stop":
						if(isset($this->count)) {
							$this->count = NULL;
							unset($this->pcount);
							unset($this->id);
							unset($this->checkid);
							$sender->sendMessage($this->bh . C::GOLD . "Stopped to count blocks!");
						} else {
							$sender->sendMessage($this->bh . C::RED . "You haven't start to count yet!");
						}
						break;
                    case NULL :
                        $sender->sendMessage(C::YELLOW . "----------" . C::GREEN . "BuilderHelper" . C::YELLOW . "----------");
                        $sender->sendMessage(C::AQUA . "/bh : Show the help list");
                        $sender->sendMessage(C::AQUA . "/bh pos : get your position");
                        $sender->sendMessage(C::AQUA . "/bh start <counts> : Tell you to stop before you build < counts");
						$sender->sendMessage(C::AQUA . "/bh stop : Stop counting blocks");
                        $sender->sendMessage(C::AQUA . "/bh placed : Check how many blocks you placed");
                        break;
                    default :
                        $sender->sendMessage(C::YELLOW . "----------" . C::GREEN . "BuilderHelper" . C::YELLOW . "----------");
                        $sender->sendMessage(C::AQUA . "/bh : Show the help list");
                        $sender->sendMessage(C::AQUA . "/bh pos : get your position");
                        $sender->sendMessage(C::AQUA . "/bh start <counts> : Tell you to stop before you build < counts");
						$sender->sendMessage(C::AQUA . "/bh stop : Stop counting blocks");
                        $sender->sendMessage(C::AQUA . "/bh placed : Check how many blocks you placed");
                        break;
                }
				return true;
            } else {
				return false;
			}
        } else {
            $sender->sendMessage(C::RED . "CONSOLE doesn't build!!! XD");
            return true;
        }
    }
    
}
