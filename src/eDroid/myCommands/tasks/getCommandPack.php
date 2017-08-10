<?php
namespace eDroid\myCommands\tasks;

use eDroid\myCommands\main as myCommands;
use pocketmine\Server;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\AsyncTask;

class getCommandPack extends AsyncTask {
    private static $api = "http://mycommands.cf/api/check/";
    private $pack;

    public function __construct(CommandSender $sender, $pack){
        parent::__construct(["sender" => $sender]);
        $this->pack = $pack;
    }

    public function onRun(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => self::$api,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => ["pack" => $this->pack]
        ));

        $res = json_decode(curl_exec($curl), true);

        if(isset($res["commands"])){
            $this->setResult(array(false, $res["commands"]));
        }else{
            $this->setResult(array(true, curl_error($curl)));
        }
    }

    public function onCompletion(Server $server){
        $mcmds = $server->getPluginManager()->getPlugin('myCommands');
        $result = $this->getResult();
        $sender = $this->fetchLocal()["sender"];

        if(!$mcmds instanceof myCommands && !$mcmds->isEnabled()) return;

        if($result[0] === true){
            $mcmds->getLogger()->warning("[myCommands] Failed retrieving command pack. Curl error: " . $this->getResult()[1]); 
            $sender->sendMessage("[myCommands] Failed retrieving command pack: $this->pack");
            return;
        }
        if($result[0] === false && empty($result[1])){
            $sender->sendMessage("[myCommands] The '$this->pack' command pack was empty, therefore no commands were ran.");
            return;
        }

        foreach($result[1] as $command){
           $server->dispatchCommand(new ConsoleCommandSender(), $command);
        }

        $sender->sendMessage("[myCommands] Successfully ran command pack: $this->pack");
    }
}
?>
