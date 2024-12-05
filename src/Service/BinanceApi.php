<?php

namespace App\Service;

class BinanceApi
{
    const CANDLE_TIME = '15m';
    const CHANGE_PERCENT = 1;

    private string $result = '';
    private array $allList = [];

    public function binanceApiInfo(array $items, SlackSendingMessage $sendingMessage): array
    {
        $symbols = $this->prepareSymbols($items);

        $apiUrl = 'https://api.binance.com/api/v3/ticker';
        $parameters = [
            'symbols' => $symbols,
            'windowSize' => self::CANDLE_TIME
        ];

        $qs = http_build_query($parameters);    // query string encode the parameters
        $request = "{$apiUrl}?{$qs}";           // create the request URL
        $curl = curl_init();                    // Get cURL resource
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl);           // Send the request, save the response
        $responseJson = json_decode($response, true); // print json decoded response

        if (!empty($responseJson)) {

            if (isset($responseJson['msg']) && $responseJson['msg'] == 'Invalid symbol.') {
                return  [$responseJson['msg'] . " in your list"];
            }

            foreach ($responseJson as $resp) {
                if ($resp["priceChangePercent"] >= self::CHANGE_PERCENT) {
                    $this->result =  str_replace("USDT", "", $resp["symbol"]) . "  =   "   . $resp["priceChangePercent"] . " 🤑";
                    $sendingMessage->sendMessage($this->result);
                    $this->allList[] = $this->result;
                } elseif ($resp["priceChangePercent"] <= -self::CHANGE_PERCENT) {
                    $this->result = str_replace("USDT", "", $resp["symbol"]) . "  =    "   . $resp["priceChangePercent"] . " 😡";
                    $sendingMessage->sendMessage($this->result);
                    $this->allList[] = $this->result;
                }
            }
        }
        curl_close($curl);                      // Close request

        return $this->allList;
    }

    /**
     * @param array $items
     * @return string
     */
    private function prepareSymbols(array $items): string
    {
        $preparedSymbols = [];
        foreach ($items as $item) {
            $preparedSymbols[] = '"' . $item . "USDT" . '"';
        }
        return "[" . implode(",", $preparedSymbols) . "]";
    }

}