<?php

namespace KnockbackEditor\forms;

use pocketmine\form\Form;
use pocketmine\player\Player;
use KnockbackEditor\Main;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

class KnockbackForm {
    private Main $plugin;
    private string $worldName;

    public function __construct(Main $plugin, string $worldName) {
        $this->plugin = $plugin;
        $this->worldName = $worldName;
    }

    public function open(Player $player): void {
        $worldName = $this->worldName;
        $form = new class($this, $worldName) implements Form {
            private KnockbackForm $parentForm;
            private string $worldName;

            public function __construct(KnockbackForm $parentForm, string $worldName) {
                $this->parentForm = $parentForm;
                $this->worldName = $worldName;
            }

            public function jsonSerialize(): array {
                return [
                    "type" => "form",
                    "title" => "Knockback - " . $this->worldName,
                    "content" => "Choose an option for knockback settings in " . $this->worldName,
                    "buttons" => [
                        ["text" => "View Knockback Settings"],
                        ["text" => "Edit Knockback Settings"]
                    ]
                ];
            }

            public function handleResponse(Player $player, $data): void {
                if ($data === null) return;
                if ($data === 0) {
                    $this->parentForm->showSettings($player);
                } elseif ($data === 1) {
                    $this->parentForm->editSettings($player);
                }
            }
        };
        
        $player->sendForm($form);
    }

    public function showSettings(Player $player): void {
        $config = $this->plugin->getKnockbackConfig();
        $worldSettings = $config->get($this->worldName, [
            "x" => 0.4,
            "y" => 0.4,
            "z" => 0.4,
            "attack-delay" => 10
        ]);

        $worldName = $this->worldName;
        $form = new class($this, $worldSettings, $worldName) implements Form {
            private KnockbackForm $parentForm;
            private array $worldSettings;
            private string $worldName;

            public function __construct(KnockbackForm $parentForm, array $worldSettings, string $worldName) {
                $this->parentForm = $parentForm;
                $this->worldSettings = $worldSettings;
                $this->worldName = $worldName;
            }

            public function jsonSerialize(): array {
                return [
                    "type" => "form",
                    "title" => "Knockback Settings - " . $this->worldName,
                    "content" => "Current knockback values:\n" .
                                 "X: {$this->worldSettings['x']}\n" .
                                 "Y: {$this->worldSettings['y']}\n" .
                                 "Z: {$this->worldSettings['z']}\n" .
                                 "Attack Delay: {$this->worldSettings['attack-delay']} ticks",
                    "buttons" => [
                        ["text" => "Close"]
                    ]
                ];
            }

            public function handleResponse(Player $player, $data): void {
                // Close form; no action needed
            }
        };
        
        $player->sendForm($form);
    }

    public function editSettings(Player $player): void {
        $config = $this->plugin->getKnockbackConfig();
        $worldSettings = $config->get($this->worldName, [
            "x" => 0.4,
            "y" => 0.4,
            "z" => 0.4,
            "attack-delay" => 10
        ]);

        $worldName = $this->worldName;
        $form = new class($this, $config, $worldSettings, $worldName) implements Form {
            private KnockbackForm $parentForm;
            private Config $config;
            private array $worldSettings;
            private string $worldName;

            public function __construct(KnockbackForm $parentForm, Config $config, array $worldSettings, string $worldName) {
                $this->parentForm = $parentForm;
                $this->config = $config;
                $this->worldSettings = $worldSettings;
                $this->worldName = $worldName;
            }

            public function jsonSerialize(): array {
                return [
                    "type" => "custom_form",
                    "title" => "Edit Knockback - " . $this->worldName,
                    "content" => [
                        ["type" => "input", "text" => "Knockback X", "default" => (string)$this->worldSettings["x"]],
                        ["type" => "input", "text" => "Knockback Y", "default" => (string)$this->worldSettings["y"]],
                        ["type" => "input", "text" => "Knockback Z", "default" => (string)$this->worldSettings["z"]],
                        ["type" => "input", "text" => "Attack Delay (ticks)", "default" => (string)$this->worldSettings["attack-delay"]]
                    ]
                ];
            }

            public function handleResponse(Player $player, $data): void {
                if ($data !== null && count($data) >= 4) {
                    $this->config->set($this->worldName, [
                        "x" => floatval($data[0]),
                        "y" => floatval($data[1]),
                        "z" => floatval($data[2]),
                        "attack-delay" => intval($data[3])
                    ]);
                    $this->config->save();
                    $player->sendMessage(TextFormat::GREEN . "Knockback settings updated for: " . $this->worldName . ".");
                } else {
                    $player->sendMessage(TextFormat::RED . "Incomplete data provided. Please check the form inputs.");
                }
            }
        };

        $player->sendForm($form);
    }
}
