"use client";

import { Button, Card, CardBody, CardHeader, Divider, Input, Select, SelectItem } from "@nextui-org/react";
import axios from "axios";
import { useState } from "react";

export default function Home() {

  const [isLoading, setIsLoading] = useState(false);

  const [symbolToEncode, setSymbolToEncode] = useState("");
  const [symbolToDecode, setSymbolToDecode] = useState("");

  const [nameEncoder, setnameEncoder] = useState("");
  const [nameDecoder, setNameDecoder] = useState("");

  const [dividerGolombEncoder, setDividerGolombEncoder] = useState("");
  const [dividerGolombDecoder, setDividerGolombDecoder] = useState("");

  const [optionChosen, setOptionChosen] = useState("");
  const [codesHuffmanEncoded, setCodesHuffmanEncoded] = useState("");
  const [codesHuffmanToDecode, setCodesHuffmanToDecode] = useState("");

  const [response, setResponse] = useState("");

  const algorithms = [
    { key: "Golomb", label: "Golomb" },
    { key: "Elias-Gamma", label: "Elias-Gamma" },
    { key: "Fibonacci/Zeckendorf", label: "Fibonacci/Zeckendorf" },
    { key: "Huffman", label: "Huffman" }
  ];

  const dividersValuesGolomb = [
    { key: "2", label: "2" },
    { key: "4", label: "4" },
    { key: "8", label: "8" },
    { key: "16", label: "16" },
    { key: "32", label: "32" },
    { key: "64", label: "64" },
    { key: "128", label: "128" },
    { key: "256", label: "256" },
    { key: "512", label: "512" },
  ];

  async function handleEncode() {
    try {
      setIsLoading(true);
      let url = '';
      switch (nameEncoder) {
        case 'Golomb':
          url = 'encode-golomb';
          break;
        case 'Elias-Gamma':
          url = 'encode-elias-gamma';
          break;
        case 'Fibonacci/Zeckendorf':
          url = 'encode-zeckendorf';
          break;
        case 'Huffman':
          url = 'encode-huffman';
          break;
        default:
          break
      }

      setOptionChosen("ENCODE");

      const response = await axios.post(`http://localhost:8000/api/${url}`, {
        "string": symbolToEncode,
        ...(nameEncoder === 'Golomb' && { divider: dividerGolombEncoder }),
      });

      const encodedString = response.data.encodedString;
      setResponse(encodedString);

      if (response.data.codes) {
        setCodesHuffmanEncoded(JSON.stringify(response.data.codes, null, 2));
      } else {
        setCodesHuffmanEncoded('');
      }


    } catch (error) {
      console.error(error);
    } finally {
      setIsLoading(false)
    }

  }

  async function handleDecode() {
    try {
      setIsLoading(true);
      let url = '';
      switch (nameDecoder) {
        case 'Golomb':
          url = 'decode-golomb';
          break;
        case 'Elias-Gamma':
          url = 'decode-elias-gamma';
          break;
        case 'Fibonacci/Zeckendorf':
          url = 'decode-zeckendorf';
          break;
        case 'Huffman':
          url = 'decode-huffman';
          break;
        default:
          break
      }

      setOptionChosen("DECODE")

      const response = await axios.post(`http://localhost:8000/api/${url}`, {
        "string": symbolToDecode,
        ...(nameDecoder === 'Golomb' && { divider: dividerGolombDecoder }),
        ...(nameDecoder === 'Huffman' && { codes: codesHuffmanToDecode })
      });

      const decodedString = response.data.decodedString;
      setResponse(decodedString);

      setCodesHuffmanEncoded('');

    } catch (error) {
      console.error(error);
    } finally {
      setIsLoading(false);
    }

  }

  const handleEncoderChange = (keys: any) => {
    setnameEncoder(Array.from(keys)[0]);
  };

  const handleDecoderChange = (keys: any) => {
    setNameDecoder(Array.from(keys)[0]);
  };

  const handleEncoderDividerChange = (keys: any) => {
    setDividerGolombEncoder(Array.from(keys)[0]);
  };

  const handleDecoderDividerChange = (keys: any) => {
    setDividerGolombDecoder(Array.from(keys)[0]);
  };

  return (
    <main className="min-h-screen w-full px-8 py-4 flex flex-col">
      <div className="flex justify-between items-center gap-8">
        {/* Codificação */}
        <Card className="w-full">
          <CardHeader className={`flex gap-2 ${optionChosen === 'ENCODE' ? 'bg-green-500' : 'bg-gray-200'}`}>
            <div className="flex flex-col text-center">
              <p className="text-md">Codificação</p>
            </div>
          </CardHeader>
          <Divider />
          <CardBody>
            <p>Preencha os dados abaixo para <b>codificar.</b></p>
            <div className="flex flex-col gap-3">
              <Input
                label="Input de Símbolos"
                labelPlacement="inside"
                variant="bordered"
                onValueChange={setSymbolToEncode}
              />
              <Select
                label="Algoritmo"
                labelPlacement="inside"
                variant="bordered"
                selectedKeys={new Set([nameEncoder])}
                onSelectionChange={handleEncoderChange}
              >
                {algorithms.map((algorithm) => (
                  <SelectItem key={algorithm.key} value={algorithm.key}>
                    {algorithm.label}
                  </SelectItem>
                ))}
              </Select>
              {nameEncoder === "Golomb" && (
                <Select
                  label="Divisor Golomb"
                  labelPlacement="inside"
                  variant="bordered"
                  selectedKeys={new Set([dividerGolombEncoder])}
                  onSelectionChange={handleEncoderDividerChange}
                >
                  {dividersValuesGolomb.map((algorithm) => (
                    <SelectItem key={algorithm.key} value={algorithm.key}>
                      {algorithm.label}
                    </SelectItem>
                  ))}
                </Select>
              )}
              <Button color="primary" onClick={handleEncode} isLoading={isLoading}>
                Codificar
              </Button>
            </div>
          </CardBody>
        </Card>

        {/* Decodificação */}
        <Card className="w-full">
          <CardHeader className={`flex gap-2 ${optionChosen === 'DECODE' ? 'bg-green-500' : 'bg-gray-200'}`}>
            <div className="flex flex-col text-center">
              <p className="text-md">Decodificação</p>
            </div>
          </CardHeader>
          <Divider />
          <CardBody>
            <p>Preencha os dados abaixo para <b>decodificar.</b></p>
            <div className="flex flex-col gap-3">
              <Input
                label="Input de Símbolos"
                labelPlacement="inside"
                variant="bordered"
                onValueChange={setSymbolToDecode}
              />
              <Select
                label="Algoritmo"
                labelPlacement="inside"
                variant="bordered"
                selectedKeys={new Set([nameDecoder])}
                onSelectionChange={handleDecoderChange}
              >
                {algorithms.map((algorithm) => (
                  <SelectItem key={algorithm.key} value={algorithm.key}>
                    {algorithm.label}
                  </SelectItem>
                ))}
              </Select>
              {nameDecoder === "Golomb" && (
                <Select
                  label="Divisor Golomb"
                  labelPlacement="inside"
                  variant="bordered"
                  selectedKeys={new Set([dividerGolombDecoder])}
                  onSelectionChange={handleDecoderDividerChange}
                >
                  {dividersValuesGolomb.map((algorithm) => (
                    <SelectItem key={algorithm.key} value={algorithm.key}>
                      {algorithm.label}
                    </SelectItem>
                  ))}
                </Select>
              )}
              {nameDecoder === "Huffman" && (
                <Input
                  label="Códigos Huffman"
                  labelPlacement="inside"
                  variant="bordered"
                  onValueChange={setCodesHuffmanToDecode}
                />
              )}
              <Button color="primary" onClick={handleDecode} isLoading={isLoading}>
                Decodificar
              </Button>
            </div>
          </CardBody>
        </Card>
      </div>
      <div className="flex-grow flex justify-center items-center">
        <p className="text-xl max-w-[90%] break-words">
          {String(response)}
          {codesHuffmanEncoded && (
            <>
              <br />
              <strong>Tabela de códigos: </strong>
              <p>{codesHuffmanEncoded}</p>
            </>
          )}
        </p>
      </div>
    </main>

  );
}
