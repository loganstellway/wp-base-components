<?php

namespace LoganStellway\Base\Helper;

use function Env\env;
use Defuse\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

class Encryption
{
    const SALT = "LSTELLWAY_SALT";

    /**
     * Build salt
     * 
     * @return string
     */
    public function buildSalt(): string
    {
        return Crypto\Key::createNewRandomKey()->saveToAsciiSafeString();
    }

    /**
     * Get salt
     * 
     * @return Crypto\Key
     * @throws \Exception
     */
    private function getSalt(): Crypto\Key
    {
        $salt = env(self::SALT);

        if (empty($salt)) {
            throw new \Exception("Encryption environment variable not set.");
        }

        return Crypto\Key::loadFromAsciiSafeString($salt);
    }

    /**
     * Encrypt a string value
     * 
     * @param string $value
     * @return string
     */
    public function encrypt(string $value): string
    {
        return Crypto\Crypto::encrypt($value, $this->getSalt());
    }

    /**
     * Decrypt a string value
     * 
     * @param string $value
     * @return string
     * @throws WrongKeyOrModifiedCiphertextException
     */
    public function decrypt(string $value): string
    {
        return Crypto\Crypto::decrypt($value, $this->getSalt());
    }
}
