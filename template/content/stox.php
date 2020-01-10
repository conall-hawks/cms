<article id="portfolio">
    <h1>Query Stock</h1>
    <table>
        <tr>
            <td>
                <fieldset>
                    <legend>Search</legend>
                    <form class="ajax" method="post">
                        <table>
                            <tr><td>
                                <input type="search" name="<?php echo $this->form_token('query'); ?>" autofocus required pattern="[\w\.]{1,5}" maxlength="5" placeholder="e.g. AAPL, BA, MCD, DIS, UNH, etc." title="Please enter a stock ticker." />
                                <script nonce="<?php echo $security->nonce(); ?>">focusInput(document.querySelector("input[name=\"<?php echo $this->form_token('query'); ?>\"]"));</script>
                            </td></tr>
                            <tr><td><div class="button-wrap" title="&quot;Gimme the loot!&quot; ~ Notorious B.I.G."><input type="submit" value="Search"></div></td></tr>
                        </table>
                    </form>
                </fieldset>
            </td>
            <td rowspan="2">
                <fieldset id="technicals">
                    <legend>Technicals</legend>
                    <p>
                        <a>Name</a>:&nbsp;<?php echo $this->model->ticker['name'] ?? 'N/A'; ?><br />

                        <ul label="Valuation">
                            <li><a href="https://en.wikipedia.org/wiki/Share_price" target="_blank">Price</a>:&nbsp;<?php echo isset($this->model->ticker['price']) ? '$'.number_format($this->model->ticker['price'], 2) : 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/d/dividend-aristocrat.asp" target="_blank" title="Generally speaking: higher is better, indicating a reliable dividend.">Years of Consecutive Dividend Increases</a>:&nbsp;<?php echo $this->model->ticker['ycdi'] ?? 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/d/dividendyield.asp" target="_blank" title="Generally speaking: higher is better. However, a yield too high may indicate a problem. As a general rule: anything over 2 is desirable, but over 5 should be scrutinized. However, it is normal for REITs and telcos be to over 5.">Dividend Yield</a>:&nbsp;<?php echo !empty($this->model->ticker['dividend_yield']) ? number_format($this->model->ticker['dividend_yield'], 2) : 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/p/price-earningsratio.asp" target="_blank" title="Generally speaking: lower is better, indicating an undervalued stock. However, a high ratio may indicate investors are anticipating future growth. As a general rule: the average is 20-25, anything vastly outside this range should be scrutinized.">Price/Earnings Ratio</a>:&nbsp;<?php echo isset($this->model->ticker['pe_ratio']) ? number_format($this->model->ticker['pe_ratio'], 2) : 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/p/pegratio.asp" target="_blank" title="Generally speaking: lower is better, indicating an undervalued stock. As a general rule: anything under 1 is desirable.">Price/Earnings/Growth Ratio</a>:&nbsp;<?php echo $this->model->ticker['peg_ratio'] ?? 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/p/price-to-salesratio.asp" target="_blank" title="Generally speaking: lower is better, indicating an undervalued stock. As a general rule: anything under 1 is desirable.">Price/Sales Ratio</a>:&nbsp;<?php echo $this->model->ticker['ps_ratio'] ?? 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/p/price-to-bookratio.asp" target="_blank" title="Generally speaking: lower is better, indicating an undervalued stock. As a general rule: anything under 1 is desirable.">Price/Book Ratio</a>:&nbsp;<?php echo $this->model->ticker['pb_ratio'] ?? 'N/A'; ?></li>
                            <li><a title="Aggregate score; higher is better.">Confidence Factor</a>:&nbsp;<?php echo !empty($this->model->ticker['confidence_factor']) ? number_format($this->model->ticker['confidence_factor'], 2) : 'N/A'; ?></li>
                        </ul>

                        <ul label="Health">
                            <li><a href="https://www.investopedia.com/terms/p/payoutratio.asp" target="_blank" title="Generally speaking: lower is better.">Payout Ratio</a>:&nbsp;<?php echo !empty($this->model->ticker['payout_ratio']) ? number_format($this->model->ticker['payout_ratio'], 2).'%' : 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/p/debtequityratio.asp" target="_blank" title="Generally speaking: lower is better. As a general rule: anything over 100 should be scrutinized. However, it is normal for REITs to over 100.">Debt/Equity Ratio</a>:&nbsp;<?php echo !empty($this->model->ticker['de_ratio']) ? number_format($this->model->ticker['de_ratio'], 2).'%' : 'N/A'; ?></li>
                            <li><a href="https://www.investopedia.com/terms/p/profitmargin.asp" target="_blank" title="Generally speaking: higher is better. As a general rule: anything over 10 is desirable.">Profit Margin</a>:&nbsp;<?php echo !empty($this->model->ticker['profit_margin']) ? number_format($this->model->ticker['profit_margin'], 2).'%' : 'N/A'; ?></li>
                        </ul>
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>
                <fieldset id="chart-fieldset">
                    <legend>Chart</legend>
                    <?php
                        $high  = 100;
                        $low = 0;
                        $count = 100;
                        $margin_x = $count * .05;
                        $margin_y = $high  * .1;
                        if(!empty($this->model->ticker)){
                            $ticker = array_reverse(json_decode($this->model->ticker['mats'], true));
                            $high   = 0;
                            $low    = PHP_INT_MAX;
                            foreach($ticker as $value){
                                if($value['5. adjusted close'] > $high) $high = $value['5. adjusted close'];
                                if($value['5. adjusted close'] < $low)  $low  = $value['5. adjusted close'];
                            }
                            $count = count($ticker);
                            $margin_x = $count * .05625;
                            $margin_y = $high  * .1;
                        }
                    ?>
                    <svg id="chart" viewbox="0 0 <?php echo ($count + $margin_x).' '.($high + $margin_y); ?>" preserveaspectratio="none">
                        <g id="matrix-group" transform="matrix(1 0 0 1 0 0)">
                            <line class="line-grid" id="y-axis" x1="<?php echo $margin_x; ?>" y1="0"                    x2="<?php echo $margin_x; ?>"          y2="<?php echo $high + $margin_y; ?>"></line>
                            <line class="line-grid"             x1="0"                        y1="<?php echo $high; ?>" x2="<?php echo $count + $margin_x; ?>" y2="<?php echo $high; ?>"></line>
                            <?php
                                if(!empty($this->model->ticker)){
                                    $i = $margin_x;
                                    foreach($ticker as $value){
                                        if(!isset($previous)){
                                            $previous = $high - $value['5. adjusted close'];
                                            $i++;
                                            continue;
                                        }
                                        $current = $high - $value['5. adjusted close'];
                                        echo '<rect class="line-area"     x="'.($i - 1).'" y="0" width="1" height="'.$high.'"><title>$'.number_format($value['5. adjusted close'], 2).'</title></rect>';
                                        echo '<line class="line"         x1="'.($i - 1).'" x2="'.$i.'" y1="'.$previous.'" y2="'.$current.'" ><title>$'.number_format($value['5. adjusted close'], 2).'</title></line>';
                                        echo '<line class="circle"       x1="'. $i     .'" x2="'.$i.'" y1="'.$current.'"  y2="'.$current.'" ><title>$'.number_format($value['5. adjusted close'], 2).'</title></line>';
                                        if($value['7. dividend amount'] > 0){
                                            echo '<line class="dividend" x1="'. $i.'"      x2="'.$i.'" y1="'.($high - $margin_y / 4).'" y2="'.($high - $margin_y / 4).'"    ><title>Dividend: $'.number_format($value['7. dividend amount'], 2).'</title></line>';
                                        }
                                        $i++;
                                        $previous = $current;
                                    }

                                    // Line color.
                                    $color = 'lime';
                                    $first = reset($ticker)['5. adjusted close'];
                                    $last  = array_reverse($ticker);
                                    $last  = reset($last)['5. adjusted close'];
                                    if($first > $last) $color = 'red';
                                }
                            ?>
                        </g>
                    </svg>
                    <style nonce="<?php echo $security->nonce(); ?>">
                        #chart-fieldset, #chart {
                            padding: 0;
                            height: 158px;
                        }
                        #chart {
                            position: absolute;
                            width: 100%;
                        }
                        #technicals {
                            height: 234px;
                        }
                        #chart line {
                            stroke: <?php echo $color ?? 'transparent'; ?>;
                            stroke-linecap: round;
                            stroke-linejoin: miter;
                            stroke-width: .1%;
                        }
                        #chart .line-area:hover + .line,
                        #chart .line-area:hover + .line + .circle,
                        #chart .line-area:hover + .line + .circle + .dividend {
                            stroke: white;
                        }
                        #chart .line-area {
                            fill: #bbb;
                            opacity: 0;
                        }
                        #chart .line-area:hover {
                            opacity: .25;
                        }
                        #chart .circle {
                            stroke-width: .15%;
                        }
                        #chart .line-grid {
                            stroke: #bbb;
                        }
                        #chart .dividend {
                            stroke: #bbb;
                            stroke-width: 1.5%;
                        }
                        #chart .dividend:hover {
                            stroke: #fff;
                        }
                    </style>
                </fieldset>
            </td>
        </tr>
    </table>

    <?php if(!empty($this->model->ticker)) echo '<pre>'.print_r($this->model->ticker, true).'</pre>'; ?>
</article>
