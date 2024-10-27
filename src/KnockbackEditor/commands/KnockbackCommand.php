<?php

namespace KnockbackEditor\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\form\Form;
use pocketmine\player\Player;
use KnockbackEditor\forms\KnockbackForm;
use KnockbackEditor\Main;

class KnockbackCommand extends Command {
    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("knockback", "View or edit knockback settings per world", "/knockback", []);
        $this->setPermission("knockbackeditor.command.knockback");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player || !$sender->hasPermission("knockbackeditor.command.knockback")) {
            $sender->sendMessage("You don't have permission to use this command.");
            return false;
        }

        $this->openWorldSelectionForm($sender);
        return true;
    }

    private function openWorldSelectionForm(Player $player): void {
        $form = new class($this->plugin) implements Form {
            private Main $plugin;

            public function __construct(Main $plugin) {
                $this->plugin = $plugin;
            }

            public function jsonSerialize(): array {
                $content = "Select a world to view or edit knockback settings.";
                $buttons = [];
                $worlds = $this->plugin->getServer()->getWorldManager()->getWorlds();

                foreach ($worlds as $world) {
                    $buttons[] = [
                        "text" => $world->getFolderName(),
                        "image" => ["type" => "path", "data" => "textures/ui/op.png"]
                    ];
                }

                return [
                    "type" => "form",
                    "title" => "Knockback Settings",
                    "content" => $content,
                    "buttons" => $buttons
                ];
            }

            public function handleResponse(Player $player, $data): void {
                if ($data === null) return;

                $worlds = array_values($this->plugin->getServer()->getWorldManager()->getWorlds());
                if (isset($worlds[$data])) {
                    $selectedWorld = $worlds[$data]->getFolderName();
                    (new KnockbackForm($this->plugin, $selectedWorld))->open($player);
                } else {
                    $player->sendMessage("Invalid world selection.");
                }
            }
        };

        $player->sendForm($form);
    }
}
