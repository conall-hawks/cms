<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="watchlist">
    <div class="scroll-head"></div>
    <div class="scroll-wrap">
        <table>
            <thead>
                <tr>
                    <td></td>
                    <td><div>Ticker</div></td>
                    <td><div>Name</div></td>
                    <td><div>Price</div></td>
                    <td><div>Years of Consecutive<br />Dividend Increases</div></td>
                    <td><div>Dividend<br />Yield</div></td>
                    <td><div>Payout<br />Ratio</div></td>
                    <td><div>Confidence<br />Factor</div></td>
                    <td><div>PE Ratio</div></td>
                    <td><div>PEG Ratio</div></td>
                    <td><div>Remove</div></td>
                </tr>
            </thead>
            <?php $stocks = $this->model->watchlist(); if($stocks) foreach($stocks as $stock){ ?>
                <tr>
                    <td id="ticker-logo-<?php echo $stock['ticker']; ?>" class="ticker-logo">
                        <style nonce="<?php echo $security->nonce(); ?>">
                            #ticker-logo-<?php echo $stock['ticker']; ?> {
                                background-image: url("<?php echo $stock['logo']; ?>");
                            }
                        </style>
                    </td>
                    <td><?php echo mb_strtoupper($stock['ticker']); ?></td>
                    <td><?php echo title($stock['name'], false); ?></td>
                    <td>$<?php echo number_format($stock['price'], 2); ?></td>
                    <td><?php echo $stock['ycdi']; ?></td>
                    <td><?php echo number_format($stock['dividend_yield'], 2); ?></td>
                    <td><?php echo number_format($stock['payout_ratio'], 2); ?>%</td>
                    <td><?php echo number_format($stock['confidence_factor'], 2); ?></td>
                    <td><?php echo number_format($stock['pe_ratio'], 2); ?></td>
                    <td><?php echo number_format($stock['peg_ratio'], 2); ?></td>
                    <td>
                        <form class="ajax" method="post">
                        <input type="hidden" name="<?php echo $this->form_token('delete'); ?>" value="<?php echo $stock['ticker']; ?>">
                        <input type="submit" value="X" />
                        </form>
                    </td>
                </tr>
            <?php } else { ?>
                <tr><td class="disabled" colspan="9">Watchlist is empty.</td></tr>
            <?php } ?>
        </table>
    </div>
</article>
