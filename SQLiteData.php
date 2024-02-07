<?php

declare(strict_types=1);

namespace Khenford\database;

interface SQLiteData{
    
    public function transferKey(string $fromUser, string $toUser, int $key): void;
    
    public function addUserData(string $user, int $key): void;
    
    public function addKey(string $user, int $key): void;
    
    public function removeKey(string $user, int $key): void;
    
    public function isUser(string $user): bool;
    
    public function getKeys(string $user): int;
}