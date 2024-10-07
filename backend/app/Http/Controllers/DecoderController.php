<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DecoderController extends Controller
{
    public function decodeByGolomb(Request $request)
    {
        $wordToDecode = (string) $request->input('string');
        $dividerValue = $request->input('divider');

        $decodedString = "";
        $suffixSize = log($dividerValue, 2);

        while (strlen($wordToDecode) > 0) {
            // Encontra o índice do bit de parada (primeiro '1')
            $stopBitIndex = strpos($wordToDecode, '1');
            if ($stopBitIndex === false) {
                break;  // Se não encontrar um '1', termina a decodificação
            }

            // Extrai o prefixo (parte com os zeros)
            $prefix = substr($wordToDecode, 0, $stopBitIndex);

            // Remove o prefixo e o bit de parada da string codificada
            $wordToDecode = substr($wordToDecode, $stopBitIndex + 1);

            // Conta quantos zeros estão no prefixo (prefixo de zeros)
            $zeroPrefix = strlen($prefix);

            // Extrai o sufixo
            $suffix = substr($wordToDecode, 0, $suffixSize);

            // Remove o sufixo da string codificada
            $wordToDecode = substr($wordToDecode, $suffixSize);

            $remainder = bindec($suffix);

            $asciiValue = $zeroPrefix * $dividerValue + $remainder;

            $decodedString .= chr($asciiValue);
        }

        return response()->json(['decodedString' => $decodedString]);
    }


    public function decodeByEliasGamma(Request $request)
    {
        $encodedMessage = $request->input('string');
        $decodedString = "";

        while (strlen($encodedMessage) > 0) {
            // Conta quantos zeros existem até encontrar o primeiro '1'
            $prefixLength = 0;
            while ($prefixLength < strlen($encodedMessage) && $encodedMessage[$prefixLength] == '0') {
                $prefixLength++;
            }

            // Pega o valor binário
            $binaryValue = substr($encodedMessage, $prefixLength, $prefixLength + 1);

            // Remove o prefixo e o binário lido da mensagem codificada
            $encodedMessage = substr($encodedMessage, $prefixLength + $prefixLength + 1);

            // Converte o valor binário de volta para decimal
            $asciiValue = bindec($binaryValue);
            $decodedString .= chr($asciiValue);
        }

        return response()->json(['decodedString' => $decodedString]);
    }

    function generateFibonacciSequence($limit)
    {
        $fibonacci = [1, 2];
        while (true) {
            $nextFib = end($fibonacci) + prev($fibonacci);
            if ($nextFib > $limit) {
                break;
            }
            $fibonacci[] = $nextFib;
        }

        return $fibonacci;
    }

    function decodeByFibonacciZeckendorf(Request $request)
    {
        $wordToDecode = $request->input('string');
        $decodedString = "";
        $fibonacciSequence = $this->generateFibonacciSequence(255);

        $i = 0;
        $currentSymbol = "";
        $encodedLength = strlen($wordToDecode);

        // Percorre cada bit no stream de bits codificados
        while ($i < $encodedLength) {
            $currentSymbol .= $wordToDecode[$i];

            if (strlen($currentSymbol) > 1 && substr($currentSymbol, -2) === "11") {
                // Decodificar o símbolo atual usando a sequência de Fibonacci
                $soma = 0;
                for ($j = 0; $j < strlen($currentSymbol) - 1; $j++) {
                    if ($currentSymbol[$j] == '1') {
                        $soma += $fibonacciSequence[$j]; // Soma os números de Fibonacci correspondentes aos '1's
                    }
                }

                $decodedString .= chr($soma);
                $currentSymbol = "";
            }

            $i++;
        }

        return response()->json(['decodedString' => $decodedString]);
    }


    public function decodeByHuffman(Request $request)
    {
        Log::info($request->input('codes'));
        $wordToDecode = $request->input('string');
        $codesString = $request->input('codes');
        $codes = array_flip(json_decode($codesString, true));
        $decodedString = "";
        $buffer = "";
        foreach (str_split($wordToDecode) as $bit) {
            $buffer .= $bit;
            if (isset($codes[$buffer])) {
                $decodedString .= $codes[$buffer];
                $buffer = "";
            }
        }

        return response()->json(['decodedString' => $decodedString]);
    }
}
