<?php

namespace App\Service;

class BinanceApi
{
    const CANDLE_TIME = '15m';
    const CHANGE_PERCENT = 1;
    const BOT_STATUS = 1;

    private string $result = '';
    private array $allList = [];
    private array $allHourList = [];

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
                return [$responseJson['msg'] . " in your list"];
            }

            if (!isset($responseJson['msg'])) {
                foreach ($responseJson as $resp) {
                    if ($resp["priceChangePercent"] >= self::CHANGE_PERCENT) {
                        $this->result = str_replace("USDT", "", $resp["symbol"]) . " " . $resp["priceChangePercent"] . " 🤑". "\n";
                        $this->allList[] = $this->result;
                    } elseif ($resp["priceChangePercent"] <= -self::CHANGE_PERCENT) {
                        $this->result = str_replace("USDT", "", $resp["symbol"]) . " " . $resp["priceChangePercent"] . " 😡". "\n";
                        $this->allList[] = $this->result;
                    }
                }

                if (!empty($this->allList)) {
                    $string = implode('', $this->allList);
                    $sendingMessage->sendMessage($string);
                }
            } else {
                $this->allList[] = $responseJson['msg'];
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

    public function binanceFuturesInfo($items, SlackSendingMessage $sendingMessage): array
    {
        $interval = "1h";  // 1-hour timeframe
        $limit = 6;       // Fetch last 6 candles
        $usdt = 'USDT';
        $consecutiveCandleCount = 5; // Number of consecutive candles needed

        // Use cURL multi-handle for parallel API requests
        $multiHandle = curl_multi_init();
        $curlHandles = [];
        $responses = [];

        foreach ($items as $item) {
            $apiUrl = "https://fapi.binance.com/fapi/v1/klines?symbol={$item}{$usdt}&interval={$interval}&limit={$limit}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($multiHandle, $ch);
            $curlHandles[$item] = $ch;
        }

        // Execute multiple requests simultaneously
        do {
            curl_multi_exec($multiHandle, $active);
        } while ($active);

        // Get responses
        foreach ($curlHandles as $item => $ch) {
            $responses[$item] = curl_multi_getcontent($ch);
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiHandle);

        // Process responses
        foreach ($responses as $item => $response) {
            $candles = json_decode($response, true);
            if (!$candles || !is_array($candles)) {
                echo "❌ Invalid data for $item.\n";
                continue;
            }

            $greenCount = 0;
            $redCount = 0;
            $lastGreenCandles = [];
            $lastRedCandles = [];

            // Process candles in a single loop
            for ($i = count($candles) - 1; $i >= 0; $i--) {
                $openPrice = (float)$candles[$i][1];
                $closePrice = (float)$candles[$i][4];

                if ($closePrice > $openPrice) {
                    $greenCount++;
                    $redCount = 0; // Reset red count
                } elseif ($closePrice < $openPrice) {
                    $redCount++;
                    $greenCount = 0; // Reset green count
                } else {
                    $greenCount = 0;
                    $redCount = 0;
                }

                if ($greenCount >= $consecutiveCandleCount) {
                    $lastGreenCandles[] = [
                        "symbol" => $item,
                    ];
                }

                if ($redCount >= $consecutiveCandleCount) {
                    $lastRedCandles[] = [
                        "symbol" => $item,
                    ];
                }
            }

//             Output result
            if (!empty($lastGreenCandles)) {
                $this->allHourList[] = "🤑 " . $item . "\n";
            }
            // Output result for red candles
            if (!empty($lastRedCandles)) {
                $this->allHourList[] = "😡 " . $item . "\n";
            }
        }
        if (!empty($this->allHourList)) {
            $string = implode('', $this->allHourList);
            $sendingMessage->sendMessage($string);
        }

        return $this->allHourList;
    }

}