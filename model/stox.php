<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**----------------------------------------------------------------------------\
| Stox database.                                                               |
\-----------------------------------------------------------------------------*/
class Stox_model extends Model {

    /* @var string: API key for alphavantage.co. */
    private $api_key_av = 'DCHCM6YYG2GK6HKX';

    /* @var string: API key for iexcloud.io. */
    private $api_key_iex = 'sk_093e63879a694bd7b9304aa8099e3215';

    /* @var string: API caches to prevent over-usage. */
    private $cache = [
        "av"  => [],
        "iex" => []
    ];

    /**------------------------------------------------------------------------\
    | Construct.                                                               |
    \-------------------------------------------------------------------------*/
    public function __construct(){

        // Connect to the database.
        parent::__construct();

        // Rebuild stox table if it doesn't already exist.
        try{$this->db->query("SELECT 1 FROM `stox` LIMIT 1");}
        catch(Exception $e){ $this->reset();}

        // Uncomment to rebuild manually.
        #$this->reset();
    }

    /**------------------------------------------------------------------------\
    | Deletes a ticker.                                                        |
    \-------------------------------------------------------------------------*/
    public function delete($ticker){
        $sql = $this->db->prepare("DELETE FROM `stox` WHERE `ticker` = :ticker");
        $sql->execute([':ticker' => $ticker]);
        $result = $sql->fetch();
        return $result;
    }

    /**------------------------------------------------------------------------\
    | Queries a ticker.                                                        |
    \-------------------------------------------------------------------------*/
    public function query($ticker, $force = false){
        $sql = $this->db->prepare("SELECT * FROM `stox` WHERE `ticker` = :ticker LIMIT 1");
        $sql->execute([':ticker' => $ticker]);
        $result = $sql->fetch();

        // Update the ticker if forced, if it doesn't exist, or is over a day old.
        if($force || !$result || $result['last_update'] < time() - 86400){
            $this->update($ticker);
            return $this->query($ticker);
        }

        // Done!
        return $result;
    }

    /**------------------------------------------------------------------------\
    | Updates a ticker.                                                        |
    \-------------------------------------------------------------------------*/
    public function update($ticker){
        logger('Updating ticker: '.$ticker);

        // Handle array argument.
        if(is_array($ticker)){
            foreach($ticker as $x) $this->update($x);
            return;
        }

        // Update timestamp.
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `last_update`
        ) VALUES(
            :ticker,
            :last_update
        ) ON DUPLICATE KEY UPDATE
            `last_update`=:last_update_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':last_update'  , time());
        $sql->bindValue(':last_update_2', time());
        $sql->execute();

        // Update metadata.
        $this->update_meta($ticker);

        // Update price.
        $this->update_price($ticker);

        // Update monthly-adjusted time series.
        $this->update_mats($ticker);

        // Update years of consecutive dividend increases.
        $this->update_ycdi($ticker);

        // Update price/earnings ratio.
        $this->update_per($ticker);

        // Update dividend yield.
        $this->update_dy($ticker);

        // Update payout ratio.
        $this->update_pr($ticker);

        // Update price/book ratio.
        $this->update_pbr($ticker);

        // Update confidence factor.
        $this->update_cf($ticker);
    }

    /*-----------------------------------------------------------------------------------------------------------------\
    | Listed below are items used to gauge confidence in continuation of dividend-increase streaks, with formulas and  |
    | point ranges.                                                                                                    |
    +-------------------------------------------------------+------------------------------------------+---------------+
    | Item                                                  | General Formula                          |  Min |    Max |
    +-------------------------------------------------------+------------------------------------------+---------------+
    | Number of Years Dividend Increased                    | Divided by 10                            | 0.50 |   5.80 |
    | Sequence Number within CCC companies                  | (1000 minus Seq) divided by 100          | 2.38 |   9.99 |
    | Dividend Reinvestment Plan                            | No-fee=2 points; Fees=1; No plan=0       | 0.00 |   2.00 |
    | Stock Purchase Plan                                   | No-fee=2 points; Fees=1; No plan=0       | 0.00 |   2.00 |
    | Most Recent Increase (percentage)                     | Increase divided by 2, up to 5 points    | 0.01 |   5.00 |
    | Payout Ratio (if not over 100% or negative)           | (100 minus payout ratio) divided by 10   | 0.00 |  10.00 |
    | Price/Earnings Ratio (if not over 100% or negative)   | (100 minus P/E ratio) divided by 10      | 0.00 |  10.00 |
    | PEG (P/E divided by Growth Rate) Ratio (if numerical) | 5 minus PEG (up to 5)                    | 0.00 |   5.00 |
    | Price/Sales Ratio                                     | 5 minus P/S (up to 5)                    | 0.00 |   5.00 |
    | Price/Book Value (if numerical)                       | 5 minus P/B (up to 5)                    | 0.00 |   5.00 |
    | This Year EPS Est Percentage Increase vs. TTM EPS     | Up to 10% Increase divided by 2          | 0.00 |   5.00 |
    | Next Year EPS Est Percentage Increase vs. TY EPS Est  | Up to 10% Increase divided by 2          | 0.00 |   5.00 |
    | Est 5-year EPS Percentage Increase                    | Up to 10% Increase divided by 2          | 0.00 |   5.00 |
    | Number of Analysts                                    | Number Divided by 10                     | 0.00 |   4.90 |
    | Market Capitalization                                 | Points for one, ten, one hundred billion | 0.00 |   3.00 |
    | Dividend Growth Rate 1-year                           | Up to 10% Increase divided by 2          | 0.01 |   5.00 |
    | Dividend Growth Rate 3-year (if numeric)              | Up to 10% Increase divided by 2          | 0.01 |   5.00 |
    | Dividend Growth Rate 5-year (if numeric)              | Up to 10% Increase divided by 2          | 0.01 |   5.00 |
    | Dividend Growth Rate 10-year (if numeric)             | Up to 10% Increase divided by 2          | 0.01 |   5.00 |
    | Mean (Simple Average)                                 | Up to 10% Increase divided by 2          | 0.01 |   5.00 |
    +-------------------------------------------------------+------------------------------------------+------+--------|
    | Total Point Range:                                                                               | 2.91 | 107.69 |
    +--------------------------------------------------------------------------------------------------+------+-------*/
    private function update_cf($ticker){
        logger('Updating confidence factor for: '.$ticker);
        $cf  = 0;
        $max = 0;

        // Get ticker data.
        $data = $this->query($ticker);

        // Calculate years of consecutive dividend increases score.
        $ycdi = $data['ycdi'] / 10;
        $cf  += $ycdi;
        $max += 6;
        logger('YCDI score: '.$ycdi.' (Min: 0.0, Max: 6.0)');
        logger('Current score: '.$cf);

        // Calculate payout ratio score.
        if(empty($data['payout_ratio'])) $data['payout_ratio'] = 50;
        if($data['payout_ratio'] >= 0 && $data['payout_ratio'] <= 100){
            $pr = (100 - $data['payout_ratio']) / 10;
        }
        $cf  += $pr;
        $max += 10;
        logger('Payout ratio score: '.$pr.' (Min: 0.0, Max: 10.0)');
        logger('Current score: '.$cf);

        // Calculate price/earnings ratio score.
        $per = 0;
        if(empty($data['pe_ratio'])) $data['pe_ratio'] = 30;
        if($data['pe_ratio'] >= 0 && $data['pe_ratio'] <= 100){
            $per = (100 - $data['pe_ratio']) / 10;
        }
        $cf  += $per;
        $max += 10;
        logger('P/E ratio score: '.$per.' (Min: 0.0, Max: 10.0)');
        logger('Current score: '.$cf);

        // Calculate price/earnings/growth ratio score.
        if(empty($data['peg_ratio'])) $data['peg_ratio'] = 0.75;
        $peg  = 5 - min(max($data['peg_ratio'], 0), 5);
        $cf  += $peg;
        $max += 5;
        logger('P/E/G ratio score: '.$peg.' (Min: 0.0, Max: 5.0)');
        logger('Current score: '.$cf);

        // Calculate price/sales ratio score.
        if(empty($data['ps_ratio'])) $data['ps_ratio'] = 0.75;
        $ps   = 5 - min(max($data['ps_ratio'], 0), 5);
        $cf  += $ps;
        $max += 5;
        logger('P/S ratio score: '.$ps.' (Min: 0.0, Max: 5.0)');
        logger('Current score: '.$cf);

        //Calculate price/book ratio score.
        if(empty($data['pb_ratio'])) $data['pb_ratio'] = 0.75;
        $pb   = 5 - min(max($data['pb_ratio'], 0), 5);
        $cf  += $pb;
        $max += 5;
        logger('P/B ratio score: '.$pb.' (Min: 0.0, Max: 5.0)');
        logger('Current score: '.$cf);

        // Feedback.
        logger('Total Score: '.$cf.'. Maximum score: '.$max);

        // Done!
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `confidence_factor`
        ) VALUES(
            :ticker,
            :confidence_factor
        ) ON DUPLICATE KEY UPDATE
            `confidence_factor`=:confidence_factor_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':confidence_factor'  , $cf);
        $sql->bindValue(':confidence_factor_2', $cf);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Update dividend yield.                                                   |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_dy($ticker){
        logger('Updating dividend yield for: '.$ticker);

        // Get monthly-adjusted time series data.
        if(!empty($this->ticker['ticker']) && $this->ticker['ticker'] === $ticker){
            $mats = $this->ticker['mats'];
        }else{
            $mats = $this->query($ticker)['mats'];
        }
        $snapshots = json_decode($mats, true);

        // Attempt to take average of last 8 dividends.
        $count     = 8;
        $dividends = [];
        foreach($snapshots as $date => $snapshot){
            if($snapshot['7. dividend amount'] > 0){
                $yield = ($snapshot['7. dividend amount'] / $snapshot['4. close']) * 4;

                // Attempt to ignore special dividends.
                foreach($dividends as $dividend){
                    if(($yield * .5) > $dividend) continue;
                }
                $dividends[] = $yield;
                $count--;
            }
            if(!$count) break;
        }

        // Done!
        $count = count($dividends);
        if($count){
            $dividend_yield = (array_sum($dividends) / $count) * 100;
            $sql = $this->db->prepare("INSERT INTO `stox`(
                `ticker`,
                `dividend_yield`
            ) VALUES(
                :ticker,
                :dividend_yield
            ) ON DUPLICATE KEY UPDATE
                `dividend_yield`=:dividend_yield_2
            ");
            $sql->bindValue(':ticker', $ticker);
            $sql->bindValue(':dividend_yield'  , $dividend_yield);
            $sql->bindValue(':dividend_yield_2', $dividend_yield);
            $sql->execute();
        }
    }

    /**------------------------------------------------------------------------\
    | Update monthly historical data.                                          |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_mats($ticker){
        logger('Updating monthly historical data: '.$ticker);

        // Get data from API.
        $url = 'https://www.alphavantage.co/query?function=TIME_SERIES_MONTHLY_ADJUSTED&symbol='.str_replace('.', '-', $ticker).'&apikey='.$this->api_key_av;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
        curl_setopt($curl, CURLOPT_URL, $url);
        $json = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($json, true);

        // Cache API query.
        $this->cache['av'][$ticker] = $json;

        // Check API result.
        if(empty($json['Monthly Adjusted Time Series'])){
            logger('API query for "'.$ticker.'" is empty.');
            return;
            #throw new Exception('API query for "'.$ticker.'" is empty.', 1);
        }

        // Done!
        $mats = json_encode($json['Monthly Adjusted Time Series']);
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `mats`
        ) VALUES(
            :ticker,
            :mats
        ) ON DUPLICATE KEY UPDATE
            `mats`=:mats_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':mats'  , $mats);
        $sql->bindValue(':mats_2', $mats);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Update metadata.                                                         |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_meta($ticker){
        logger('Updating metadata for: '.$ticker);

        // Get data; check API cache first.
        if(!empty($this->cache['iex'][$ticker]['stats'])){
            $json = $this->cache['iex'][$ticker]['stats'];
        }else{
            // Get data from API.
            $url = 'https://cloud.iexapis.com/v1/stock/'.$ticker.'/stats?token='.$this->api_key_iex;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
            curl_setopt($curl, CURLOPT_URL, $url);
            $json = curl_exec($curl);
            curl_close($curl);

            // Cache API query.
            $this->cache['iex'][$ticker]['stats'] = $json;
        }

        // Set name.
        $json = json_decode($json, true);

        // Validate response.
        if(empty($json['companyName'])){
            logger('IEX API query "stats" for "'.$ticker.'" is empty.');
            return;
        }

        // Perform normalizations.
        $name = title($json['companyName'], false);
        $name = preg_replace('/\b\s*american\s+depositary\s+shares\b/i', '', $name);
        $name = preg_replace('/\s*class\s*[abc]/i'                     , '', $name);
        $name = preg_replace('/\b\s*common\s+stock\b/i'                , '', $name);
        $name = preg_replace('/\b\s*corporation\b/i'                   , '', $name);
        $name = preg_replace('/\b\s*each\s+representing\s+two\b/i'     , '', $name);
        $name = preg_replace('/\b\s*group\b/i'                         , '', $name);
        $name = preg_replace('/\b\s*incorporated\b/i'                  , '', $name);
        $name = preg_replace('/\b\s*snats\b/i'                         , '', $name);
        $name = preg_replace('/\b,?\s*ag\b\.?/i'                       , '', $name);
        $name = preg_replace('/\b\s*&?\s*co\b\.?/i'                    , '', $name);
        $name = preg_replace('/\b,?\s*corp\b\.?/i'                     , '', $name);
        $name = preg_replace('/\b,?\s*inc\b\.?/i'                      , '', $name);
        $name = preg_replace('/\b,?\s*lp/i'                            , '', $name);
        $name = preg_replace('/\b,?\s*ltd\b\.?/i'                      , '', $name);
        $name = preg_replace('/\b,?\s*plc\b\.?/i'                      , '', $name);
        $name = preg_replace('/\b,*\s*\(*the\)*\s*/i'                  , '', $name);

        // Get data; check API cache first.
        if(!empty($this->cache['iex'][$ticker]['logo'])){
            $json = $this->cache['iex'][$ticker]['logo'];
        }else{
            // Get data from API.
            $url = 'https://cloud.iexapis.com/v1/stock/'.$ticker.'/logo?token='.$this->api_key_iex;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
            curl_setopt($curl, CURLOPT_URL, $url);
            $json = curl_exec($curl);
            curl_close($curl);

            // Cache API query.
            $this->cache['iex'][$ticker]['stats'] = $json;
        }

        // Set logo.
        $json = json_decode($json, true);
        $logo = $json['url'];

        // Save into database.
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `name`,
            `logo`
        ) VALUES(
            :ticker,
            :name,
            :logo
        ) ON DUPLICATE KEY UPDATE
            `name`=:name_2,
            `logo`=:logo_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':name'  , $name);
        $sql->bindValue(':name_2', $name);
        $sql->bindValue(':logo'  , $logo);
        $sql->bindValue(':logo_2', $logo);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Update price/book ratio.                                                 |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_pbr($ticker){
        logger('Updating price/book ratio for: '.$ticker.'.');

        // Get data; check API cache first.
        if(!empty($this->cache['iex'][$ticker]['stats'])){
            $json = $this->cache['iex'][$ticker]['stats'];
        }else{
            // Get data from API.
            $url = 'https://cloud.iexapis.com/v1/stock/'.$ticker.'/advanced-stats?token='.$this->api_key_iex;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
            curl_setopt($curl, CURLOPT_URL, $url);
            $json = curl_exec($curl);
            curl_close($curl);

            // Cache API query.
            $this->cache['iex'][$ticker]['stats'] = $json;
        }

        // Set price/book ratio.
        $json = json_decode($json, true);
        if(empty($json['priceToBook'])){
            logger('Unable to update price/book ratio for: '.$ticker.'.');
            return;
        }
        $pb_ratio = $json['priceToBook'];
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `pb_ratio`
        ) VALUES(
            :ticker,
            :pb_ratio
        ) ON DUPLICATE KEY UPDATE
            `pb_ratio`=:pb_ratio_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':pb_ratio'  , $pb_ratio);
        $sql->bindValue(':pb_ratio_2', $pb_ratio);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Update price/earnings ratio.                                             |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_per($ticker){
        logger('Updating price/earnings ratio for: '.$ticker);

        /* Get data; check API cache first. */
        if(!empty($this->cache['yahoo'][$ticker]['key-statistics'])){
            $json = $this->cache['yahoo'][$ticker]['key-statistics'];
        }

        /* Get data from API. */
        else{
            $url = 'https://finance.yahoo.com/quote/'.str_replace('.', '-', $ticker).'/key-statistics';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
            curl_setopt($curl, CURLOPT_URL, $url);
            $result = curl_exec($curl);
            curl_close($curl);
        }

        /* Prevent negligible errors from polluting HTML output. */
        libxml_use_internal_errors(true);

        /* Build DOM */
        $html = new DOMDocument();
        $html->loadHTML($result);

        /* Locate values. */
        $pe_ratio  = 0;
        $peg_ratio = 0;
        $elements = $html->getElementsByTagName('span');
        foreach($elements as $element){
            switch(trim($element->nodeValue)){
                case 'Total Debt/Equity':
                    $value = preg_replace('/[^-\d.]/', '', $element->parentNode->nextSibling->nodeValue);
                    if(is_numeric($value)){
                        $sql = $this->db->prepare("INSERT INTO `stox`(
                            `ticker`,
                            `de_ratio`
                        ) VALUES(
                            :ticker,
                            :de_ratio
                        ) ON DUPLICATE KEY UPDATE
                            `de_ratio`=:de_ratio_2
                        ");
                        $sql->bindValue(':ticker', $ticker);
                        $sql->bindValue(':de_ratio'  , $value);
                        $sql->bindValue(':de_ratio_2', $value);
                        $sql->execute();
                    }
                    break;
                case 'Trailing P/E':
                    $value = preg_replace('/[^-\d.]/', '', $element->parentNode->nextSibling->nodeValue);
                    if(is_numeric($value)){
                        $sql = $this->db->prepare("INSERT INTO `stox`(
                            `ticker`,
                            `pe_ratio`
                        ) VALUES(
                            :ticker,
                            :pe_ratio
                        ) ON DUPLICATE KEY UPDATE
                            `pe_ratio`=:pe_ratio_2
                        ");
                        $sql->bindValue(':ticker', $ticker);
                        $sql->bindValue(':pe_ratio'  , $value);
                        $sql->bindValue(':pe_ratio_2', $value);
                        $sql->execute();
                    }
                    break;
                case 'PEG Ratio (5 yr expected)':
                    $value = preg_replace('/[^-\d.]/', '', $element->parentNode->nextSibling->nodeValue);
                    if(is_numeric($value)){
                        $sql = $this->db->prepare("INSERT INTO `stox`(
                            `ticker`,
                            `peg_ratio`
                        ) VALUES(
                            :ticker,
                            :peg_ratio
                        ) ON DUPLICATE KEY UPDATE
                            `peg_ratio`=:peg_ratio_2
                        ");
                        $sql->bindValue(':ticker', $ticker);
                        $sql->bindValue(':peg_ratio'  , $value);
                        $sql->bindValue(':peg_ratio_2', $value);
                        $sql->execute();
                    }
                    break;
                case 'Price/Book':
                    $value = preg_replace('/[^-\d.]/', '', $element->parentNode->nextSibling->nodeValue);
                    if(is_numeric($value)){
                        $sql = $this->db->prepare("INSERT INTO `stox`(
                            `ticker`,
                            `pb_ratio`
                        ) VALUES(
                            :ticker,
                            :pb_ratio
                        ) ON DUPLICATE KEY UPDATE
                            `pb_ratio`=:pb_ratio_2
                        ");
                        $sql->bindValue(':ticker', $ticker);
                        $sql->bindValue(':pb_ratio'  , $value);
                        $sql->bindValue(':pb_ratio_2', $value);
                        $sql->execute();
                    }
                    break;
                case 'Price/Sales':
                    $value = preg_replace('/[^-\d.]/', '', $element->parentNode->nextSibling->nodeValue);
                    if(is_numeric($value)){
                        $sql = $this->db->prepare("INSERT INTO `stox`(
                            `ticker`,
                            `ps_ratio`
                        ) VALUES(
                            :ticker,
                            :ps_ratio
                        ) ON DUPLICATE KEY UPDATE
                            `ps_ratio`=:ps_ratio_2
                        ");
                        $sql->bindValue(':ticker', $ticker);
                        $sql->bindValue(':ps_ratio'  , $value);
                        $sql->bindValue(':ps_ratio_2', $value);
                        $sql->execute();
                    }
                    break;
                case 'Profit Margin':
                    $value = preg_replace('/[^-\d.]/', '', $element->parentNode->nextSibling->nodeValue);
                    if(is_numeric($value)){
                        $sql = $this->db->prepare("INSERT INTO `stox`(
                            `ticker`,
                            `profit_margin`
                        ) VALUES(
                            :ticker,
                            :profit_margin
                        ) ON DUPLICATE KEY UPDATE
                            `profit_margin`=:profit_margin_2
                        ");
                        $sql->bindValue(':ticker', $ticker);
                        $sql->bindValue(':profit_margin'  , $value);
                        $sql->bindValue(':profit_margin_2', $value);
                        $sql->execute();
                    }
                    break;
            }
        }
    }
#    private function update_per($ticker){
#        logger('Updating price/earnings ratio for: '.$ticker);
#
#        // Get data; check API cache first.
#        if(!empty($this->cache['iex'][$ticker]['earnings'])){
#            $json = $this->cache['iex'][$ticker]['earnings'];
#        }else{
#            // Get data from API.
#            $url = 'https://cloud.iexapis.com/v1/stock/'.$ticker.'/earnings?token='.$this->api_key_iex;
#            $curl = curl_init();
#            curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
#            curl_setopt($curl, CURLOPT_URL, $url);
#            $json = curl_exec($curl);
#            curl_close($curl);
#
#            // Cache API query.
#            $this->cache['iex'][$ticker]['earnings'] = $json;
#        }
#
#        // Get earnings data.
#        $json = json_decode($json, true);
#        if(!isset($json['earnings'])) logger('Missing earnings data.');
#        if(!isset($json['earnings'][0])) logger('Missing earnings time slice.');
#        if(!isset($json['earnings'][0]['actualEPS'])) logger('Missing EPS data.');
#
#        // Attempt to iterate over last 4 quarters.
#        $egr_avg = 0;
#        $egr_cnt = 0;
#        $eps_cnt = 0;
#        $eps_ttm = 0;
#        for($i = 0; $i < 3; $i++){
#            if(isset($json['earnings'][$i]['actualEPS'])){
#
#                // Calculate EGR.
#                if(isset($eps)){
#                    $egr_avg += ($json['earnings'][$i]['actualEPS'] - $eps) / $eps;
#                    $egr_cnt++;
#                }
#
#                // Calculate EPS.
#                $eps = $json['earnings'][$i]['actualEPS'];
#                $eps_ttm += $eps;
#                $eps_cnt++;
#            }
#        }
#        // EGR average, percentage-based.
#        $egr_avg = $egr_cnt ? ($egr_avg / $egr_cnt) * 100 : 0;
#
#        // Coalesce missing quarters for TTM EPS.
#        while($eps_cnt > 0 && $eps_cnt < 4){
#            $eps_ttm += $eps_ttm / $eps_cnt;
#            $eps_cnt++;
#        }
#
#        // Get monthly-adjusted time series data.
#        if(!empty($this->ticker['ticker']) && $this->ticker['ticker'] === $ticker){
#            $mats = $this->ticker['mats'];
#        }else{
#            $mats = $this->query($ticker)['mats'];
#        }
#        $snapshots = json_decode($mats, true);
#
#        // Calculate PE ratio.
#        $price = array_values($snapshots)[0]['4. close'];
#        $pe_ratio = $eps_ttm ? $price / $eps_ttm : 0;
#
#        // Save.
#        $sql = $this->db->prepare("INSERT INTO `stox`(
#            `ticker`,
#            `pe_ratio`
#        ) VALUES(
#            :ticker,
#            :pe_ratio
#        ) ON DUPLICATE KEY UPDATE
#            `pe_ratio`=:pe_ratio_2
#        ");
#        $sql->bindValue(':ticker', $ticker);
#        $sql->bindValue(':pe_ratio'  , $pe_ratio);
#        $sql->bindValue(':pe_ratio_2', $pe_ratio);
#        $sql->execute();
#
#        ## Temoporary.
#        $peg_ratio = $egr_avg ? $pe_ratio / $egr_avg : 0;
#        $sql = $this->db->prepare("INSERT INTO `stox`(
#            `ticker`,
#            `peg_ratio`
#        ) VALUES(
#            :ticker,
#            :peg_ratio
#        ) ON DUPLICATE KEY UPDATE
#            `peg_ratio`=:peg_ratio_2
#        ");
#        $sql->bindValue(':ticker', $ticker);
#        $sql->bindValue(':peg_ratio'  , $peg_ratio);
#        $sql->bindValue(':peg_ratio_2', $peg_ratio);
#        $sql->execute();
#    }

    /**------------------------------------------------------------------------\
    | Update payout ratio.                                                     |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_pr($ticker){
        logger('Updating payout ratio for: '.$ticker);

        // Get monthly-adjusted time series data.
        if(!empty($this->ticker['ticker']) && $this->ticker['ticker'] === $ticker){
            $mats = $this->ticker['mats'];
        }else{
            $mats = $this->query($ticker)['mats'];
        }
        $snapshots = json_decode($mats, true);

        // Get data from API.
        $url = 'https://cloud.iexapis.com/v1/stock/'.$ticker.'/stats?token='.$this->api_key_iex;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
        curl_setopt($curl, CURLOPT_URL, $url);
        $json = curl_exec($curl);
        curl_close($curl);

        // Cache API query.
        $this->cache['iex'][$ticker]['stats'] = $json;

        // Calculate trailing twelve months' payout ratio.
        $json = json_decode($json, true);
        $payout_ratio = $json['ttmEPS'] ? ($json['ttmDividendRate'] / $json['ttmEPS']) * 100 : 0;
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `payout_ratio`
        ) VALUES(
            :ticker,
            :payout_ratio
        ) ON DUPLICATE KEY UPDATE
            `payout_ratio`=:payout_ratio_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':payout_ratio'  , $payout_ratio);
        $sql->bindValue(':payout_ratio_2', $payout_ratio);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Update the price.                                                        |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_price($ticker){

        // Get data; check API cache first.
        if(!empty($this->cache['iex'][$ticker]['price'])){
            $json = $this->cache['iex'][$ticker]['price'];
        }else{
            // Get data from API.
            #$url = 'https://cloud.iexapis.com/v1/stock/'.$ticker.'/price?token='.$this->api_key_iex;
            $url = 'https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol='.str_replace('.', '-', $ticker).'&apikey='.$this->api_key_av;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_URL, $url);
            $json = curl_exec($curl);
            curl_close($curl);

            // Cache API query.
            $this->cache['iex'][$ticker]['price'] = $json;
        }

        // Set price.
        #$price = number_format(json_decode($json, true), 2);
        try{
            $price = number_format(json_decode($json, true)["Global Quote"]["05. price"], 2);
        }catch(Exception $error){
            logger($error);
            $price = 0.00;
        }

        // Save into database.
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `price`
        ) VALUES(
            :ticker,
            :price
        ) ON DUPLICATE KEY UPDATE
            `price`=:price_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':price'  , $price);
        $sql->bindValue(':price_2', $price);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Update the number of years of consecutive dividend increases.            |
    +---------+--------+---------+---------------------------------------------+
    | @param  | string | $ticker | Ticker of stock.                            |
    \---------+--------+---------+--------------------------------------------*/
    private function update_ycdi($ticker){
        logger('Updating years of consecutive dividend increases for: '.$ticker.'.');

        // Get monthly-adjusted time series data.
        if(!empty($this->ticker['ticker']) && $this->ticker['ticker'] === $ticker){
            $mats = $this->ticker['mats'];
        }else{
            $mats = $this->query($ticker)['mats'];
        }

        if(!$mats){
            logger('Unable to update years of consecutive dividend increases for: '.$ticker.'.');
            return;
        }

        // Calculate years of consecutive dividend increases.
        $snapshots = array_reverse(json_decode($mats, true));
        $previous_dividend[0] = 0;
        $previous_dividend[1] = 0;
        $previous_dividend[2] = 0;
        $previous_dividend[3] = 0;
        $years = [];
        foreach($snapshots as $date => $snapshot){

            // Check for dividend.
            $dividend = $snapshot['7. dividend amount'];
            if($dividend > 0){

                // Get year.
                $date = new DateTime($date);
                $year = $date->format('Y');

                // Compare year-over-year.
                if(!in_array($year, $years)){

                    // Check if this dividend is greater than or equal to
                    // previous dividends. Compare against previous few
                    // dividends to account for special dividends.
                    if($dividend > $previous_dividend[0]
                    || $dividend > $previous_dividend[1]
                    || $dividend > $previous_dividend[2]
                    || $dividend > $previous_dividend[3]){

                        // Consecutive dividend increase, mark year.
                        $years[] = $year;


                    }
                    // The dividend was cut, reset consecutive years.
                    else{
                        $years = [$year];
                    }

                    // Mark the previous few dividends for comparison.
                    $previous_dividend[3] = $previous_dividend[2];
                    $previous_dividend[2] = $previous_dividend[1];
                    $previous_dividend[1] = $previous_dividend[0];
                    $previous_dividend[0] = $dividend;
                }
            }
        }

        // Done!
        $ycdi = count($years);
        $sql = $this->db->prepare("INSERT INTO `stox`(
            `ticker`,
            `ycdi`
        ) VALUES(
            :ticker,
            :ycdi
        ) ON DUPLICATE KEY UPDATE
            `ycdi`=:ycdi_2
        ");
        $sql->bindValue(':ticker', $ticker);
        $sql->bindValue(':ycdi'  , $ycdi);
        $sql->bindValue(':ycdi_2', $ycdi);
        $sql->execute();
    }

    /**------------------------------------------------------------------------\
    | Queries a watchlist.                                                     |
    \-------------------------------------------------------------------------*/
    public function watchlist($order = 'confidence_factor'){
        $order = trim($this->db->quote($order, PDO::PARAM_STR), "'");
        $sql = $this->db->query("SELECT * FROM `stox` ORDER BY `$order` DESC;");
        return $sql->fetchAll();
    }

    /**------------------------------------------------------------------------\
    | Rebuild the databases.                                                   |
    +---------+-----------+-----------+----------------------------------------+
    | @param  | string    | $db_name  | Name of the database.                  |
    \-------------------------------------------------------------------------*/
    private function reset(){
        logger('Rebuilding database: stox.');

        // Erase upload table.
        $this->db->exec("DROP TABLE IF EXISTS `stox`;");

        // Create upload table.
        $this->db->exec("
            CREATE TABLE `stox` (
                `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Auto-incrementing ID of each stock, unique index.',
                `ticker`            CHAR(255)    COMMENT 'Stock ticker.',
                `name`              CHAR(255)    COMMENT 'Company name.',
                `logo`              TEXT         COMMENT 'URL to company logo.',
                `price`             FLOAT        COMMENT 'Price.',
                `mats`              TEXT         COMMENT 'Monthly adjusted time-series data in JSON format.',
                `ycdi`              INT UNSIGNED COMMENT 'Years of Consecutive Dividend Increases.',
                `dividend_yield`    FLOAT        COMMENT 'Dividend yield.',
                `de_ratio`          FLOAT        COMMENT 'Debt/Equity ratio.',
                `pe_ratio`          FLOAT        COMMENT 'Price/Earnings ratio.',
                `peg_ratio`         FLOAT        COMMENT 'Price/Earnings/Growth ratio.',
                `ps_ratio`          FLOAT        COMMENT 'Price/Sales ratio.',
                `pb_ratio`          FLOAT        COMMENT 'Price/Book ratio.',
                `payout_ratio`      FLOAT        COMMENT 'Payout ratio.',
                `profit_margin`     FLOAT        COMMENT 'Profit margin.',
                `confidence_factor` FLOAT        COMMENT 'Confidence factor.',
                `gics_sector`       CHAR(255)    COMMENT 'GICS sector.',
                `gics_industry`     CHAR(255)    COMMENT 'GICS industry.',
                `gics_subindustry`  CHAR(255)    COMMENT 'GICS sub-industry.',
                `last_update`    INT UNSIGNED COMMENT 'UNIX timestamp of last update.'
            ) AUTO_INCREMENT=".rand(32768, 65536)." CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ROW_FORMAT=COMPRESSED COMMENT 'Stock data.';
        ");

        // Add long prefix constraint.
        #$this->db->exec("SET GLOBAL innodb_file_format    = `BARRACUDA`;");
        #$this->db->exec("SET GLOBAL innodb_large_prefix   = `ON`;");
        $this->db->exec("SET GLOBAL innodb_file_per_table = `ON`;");
        $this->db->exec("ALTER TABLE `stox` ADD UNIQUE (`ticker`);");
        $this->db->exec("ALTER TABLE `stox` ADD UNIQUE (`name`);");
    }
}
