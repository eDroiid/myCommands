<?php
namespace eDroid\myCommands;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use eDroid\myCommands\tasks\getCommandPack;
use pocketmine\utils\Utils;

class main extends PluginBase {
	public function onEnable() {
		$this->getLogger()->info("myCommands is booting up");
		if(Utils::$online == false) $this->getLogger()->warning("myCommands will not work properly if offline!");
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
		$cmd = strtolower($command->getName());
	    if($cmd == "myCommands"){
	    	if(isset($args[0])){
	    		if($args[0] == "help"){
	    			$sender->sendMessage("[myCommands] Run a multiple amount of commands of any lengths with ease!");
	    			$sender->sendMessage("[myCommands] First head over to http://myCommands.cf/");
	    			$sender->sendMessage("[myCommands] Then click 'Create Command Pack'");
	    			$sender->sendMessage("[myCommands] Then add as many commands as you please");
	    			$sender->sendMessage("[myCommands] Finally press 'Create' and run the command given to you");
	    		}elseif($args[0] == "run"){
	    			if(isset($args[1])){
	    				$sender->sendMessage("[myCommands] Attempting to get command pack: " . $args[1]);
	    				$this->getServer()->getScheduler()->scheduleAsyncTask(new getCommandPack($sender, $args[1]));
	    			}else{
	    				$sender->sendMessage("[myCommands] Please provide a pack! or do /myCommands help");
	    			}
	    		}
	    	}else{
	    		$sender->sendMessage("[myCommands] /myCommands < help | run > [args]");
	    	}
	    }
	}
	public function onDisable() {
		$this->getLogger()->info("myCommands is shutting down");
	}
}
?>