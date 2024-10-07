<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SplPriorityQueue;

class EncoderController extends Controller
{
    public function encodeByGolomb(Request $request)
    {
        $wordToEncode = $request->input('string');
        $dividerValue = $request->input('divider');
        $encodedString = "";

        $asciiWord = [];
        for ($i = 0; $i < strlen($wordToEncode); $i++) {
            $asciiWord[] = ord($wordToEncode[$i]);
        }

        foreach ($asciiWord as $word) {
            $quotient = intdiv($word, $dividerValue);
            $rest = $word % $dividerValue;

            $encodedQuotient = str_repeat('0', $quotient) . '1';

            $bitsNeeded = ceil(log($dividerValue, 2));
            $encodedRemainder = str_pad(decbin($rest), $bitsNeeded, '0', STR_PAD_LEFT);

            $encodedString .= $encodedQuotient . $encodedRemainder;
        }

        return response()->json(['encodedString' => $encodedString]);
    }


    public function encodeByEliasGamma(Request $request)
    {
        $wordToEncode = $request->input('string');
        $encodedString = "";

        for ($i = 0; $i < strlen($wordToEncode); $i++) {
            $asciiValue = ord($wordToEncode[$i]);

            $binaryValue = decbin($asciiValue);

            // Calcula o prefixo
            $prefix = str_repeat('0', strlen($binaryValue) - 1);

            // Combina o prefixo e o valor binário
            $encodedChar = $prefix . $binaryValue;

            $encodedString .= $encodedChar;
        }

        return response()->json(['encodedString' => $encodedString]);
    }

    function generateFibonacciSequence($limit)
    {
        $fibonacci = [1, 2]; // Inicia com 1,2
        while (true) {
            $nextFib = end($fibonacci) + prev($fibonacci);
            if ($nextFib > $limit) {
                break;
            }
            $fibonacci[] = $nextFib;
        }

        return $fibonacci;
    }

    public function encodeByFibonacciZeckendorf(Request $request)
    {
        $wordToEncode = $request->input('string');
        $encodedString = "";

        $asciiValues = [];
        for ($i = 0; $i < strlen($wordToEncode); $i++) {
            $asciiValues[] = ord($wordToEncode[$i]);
        }

        foreach ($asciiValues as $asciiValue) {
            $encodedWord = [];
            $fibonacciSequence = $this->generateFibonacciSequence($asciiValue);
            $remainingSum = $asciiValue;

            // Faz a soma com os números de Fibonacci, de trás para frente
            foreach (array_reverse($fibonacciSequence) as $fib) {
                if ($fib <= $remainingSum) {
                    array_unshift($encodedWord, 1);
                    $remainingSum -= $fib;
                } else {
                    array_unshift($encodedWord, 0);
                }
            }

            // Adiciona Stop Bit
            $encodedWord[] = 1;

            // Converte a array de bits em uma string
            $newEncodedWord = implode("", $encodedWord);
            $encodedString .= $newEncodedWord;
        }

        return response()->json(['encodedString' => $encodedString]);
    }


    function createFrequencyHuffmanTable($string)
    {
        $freqTable = [];
        foreach (str_split($string) as $char) {
            if (!isset($freqTable[$char])) {
                $freqTable[$char] = 0;
            }
            $freqTable[$char]++;
        }
        return $freqTable;
    }

    function createHuffmanTree($freqTable)
    {
        $nodes = [];

        // Inicializa os nós com base na frequência
        foreach ($freqTable as $char => $freq) {
            $nodes[] = [$freq, $char, null, null]; // [frequência, caractere, esquerda, direita]
        }

        // Enquanto houver mais de um nó, continue a mesclá-los
        while (count($nodes) > 1) {
            // Ordena nós pela frequência
            usort($nodes, function ($a, $b) {
                return $a[0] - $b[0]; // Comparando as frequências
            });

            // Pega os dois nós com menor frequência
            $left = array_shift($nodes);
            $right = array_shift($nodes);

            // Cria um novo nó combinando as frequências
            $newNode = [$left[0] + $right[0], null, $left, $right];

            // Insere o novo nó na lista
            $nodes[] = $newNode;
        }

        // Retorna a raiz da árvore
        return $nodes[0];
    }

    function generateCodes($node, $code = "", &$codes = [])
    {
        if ($node[1] !== null) {
            // Se for uma folha (nó com caractere)
            $codes[$node[1]] = $code;
        } else {
            // Se for um nó interno, continua a gerar os códigos para a esquerda e direita
            $this->generateCodes($node[2], $code . "0", $codes);
            $this->generateCodes($node[3], $code . "1", $codes);
        }
    }

    public function encodeByHuffman(Request $request)
    {
        $wordToEncode = $request->input('string');

        $freqTable = $this->createFrequencyHuffmanTable($wordToEncode);

        // Construir a árvore de Huffman
        $huffmanTree = $this->createHuffmanTree($freqTable);

        // Gerar os códigos de Huffman para cada caractere
        $codes = [];
        $this->generateCodes($huffmanTree, "", $codes);

        // Codificar a string, garantindo que o espaço seja tratado como qualquer outro caractere
        $encodedString = "";
        foreach (str_split($wordToEncode) as $char) {
            // Verifica se o caractere está na tabela de códigos
            if (isset($codes[$char])) {
                $encodedString .= $codes[$char];
            }
        }

        return response()->json(['encodedString' => $encodedString, 'codes' => $codes]);
    }
}
