<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller {
    public function generateEncryptedData(Request $request) {
        $amount = $request->input('amount');

        if(true) { // debug
            $amount = 1.00;
        }

        $data = [
            'merchantId' => '9391636',
            'name' => 'portal',
            'password' => 'Fu5hYM#5#G',
            'mode' => 'AUT', // or 'PRD' for production
            'controlNumber' => time(),
            'terminalId' => '93916361',
            'amount' => $amount,
            'lang' => 'ES'
        ];

        $encrypted_data = $this->encryptData($data);

        return response()->json(['encrypted_data' => $encrypted_data]);
    }

    private function encryptData($data) {
        // Load the certificate
        $certPath = storage_path('app/certificates/multicobroskeyunixbanortecom.cer');
        $publicKey = openssl_pkey_get_public(file_get_contents($certPath));
    
        if ($publicKey === false) {
            throw new \Exception('Unable to load public key');
        }
        
        // Generate AES key, IV, and salt
        $aesKey = random_bytes(16);
        $iv = random_bytes(16);
        $salt = random_bytes(16);
    
        // Convert to hexadecimal
        $aesKeyHex = bin2hex($aesKey);
        $ivHex = bin2hex($iv);
        $saltHex = bin2hex($salt);
        
        // Use the AES key, IV, and salt to encrypt the data
        $aesEncrypted = openssl_encrypt($data, 'aes-256-ctr', $aesKey, OPENSSL_RAW_DATA, $iv);
        
        if ($aesEncrypted === false) {
            throw new \Exception('AES encryption failed: ' . openssl_error_string());
        }
        
         // Create passphrase by combining aesKey, iv, and salt
        $passphrase = substr(base64_encode($aesKey . $iv . $salt), 0, 16);
    
        // Generate Subcadena2: Encrypt the data with AES
        $subcadena2 = base64_encode($aesEncrypted);
        
        // Generate Subcadena1: Encrypt passphrase using RSA
        $pData = $ivHex . '::' . $saltHex . '::' . $passphrase;
        if (!openssl_public_encrypt($pData, $subcadena1Encrypted, $publicKey)) {
            throw new Exception('RSA encryption failed: ' . openssl_error_string());
        }
        $subcadena1 = base64_encode($subcadena1Encrypted);
    
        // Concatenate Subcadena1 and Subcadena2 with ":::"
        $payload = $subcadena1 . ':::' . $subcadena2;
    
        return $payload;
    }
}
