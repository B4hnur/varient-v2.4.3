<div class="row">
    <div class="col-sm-12 title-section">
        <h3><?= trans('my_earnings'); ?></h3>
    </div>
</div>
<div class="row" style="display: flex; justify-content: center">
    <div class="col-lg-3 col-xs-6" style="max-width: 420px;">
        <div class="small-box admin-small-box bg-primary">
            <div class="inner">
                <h3><?= numberFormatShort(user()->total_pageviews); ?></h3>
                <p><?= trans("total_pageviews"); ?></p>
            </div>
            <div class="icon">
                <i class="fa fa-bar-chart-o"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6" style="max-width: 420px;">
        <div class="small-box admin-small-box bg-success">
            <div class="inner">
                <h3><?= priceFormatted(user()->balance); ?></h3>
                <p><?= trans("balance"); ?></p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="box">
            <div class="box-body">
                <div style="min-height: 400px;">
                    <canvas id="chart-1"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="box">
            <div class="box-body">
                <div style="min-height: 400px;">
                    <canvas id="chart-2"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("payouts"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('set-payout-account'); ?>" class="btn btn-primary btn-add-new"><i class="fa fa-credit-card"></i><?= trans("set_payout_account"); ?></a>&nbsp;
                    <button type="button" class="btn btn-success btn-add-new" data-toggle="modal" data-target="#modalNewPayout"><i class="fa fa-plus"></i><?= trans("new_payout_request"); ?></button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped dataTable" id="cs_datatable" role="grid" aria-describedby="example1_info">
                                <thead>
                                <tr role="row">
                                    <th><?= trans('id'); ?></th>
                                    <th><?= trans('amount'); ?></th>
                                    <th><?= trans('payout_method'); ?></th>
                                    <th><?= trans('status'); ?></th>
                                    <th><?= trans('date'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($payouts)):
                                    foreach ($payouts as $item): ?>
                                        <tr>
                                            <td><?= $item->id; ?></td>
                                            <td class="font-w-700"><?= priceFormatted($item->amount); ?></td>
                                            <td><?= trans($item->payout_method); ?></td>
                                            <td>
                                                <?php if ($item->status == 1): ?>
                                                    <label class="label label-success"><?= trans("completed"); ?></label>
                                                <?php else: ?>
                                                    <label class="label label-warning"><?= trans("pending"); ?></label>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= formatDateFront($item->created_at); ?></td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("earnings"); ?></h3>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" role="grid">
                                <thead>
                                <tr role="row">
                                    <th><?= trans('date'); ?></th>
                                    <th><?= trans('pageviews'); ?></th>
                                    <th><?= trans('earnings'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($pageViewsCounts)):
                                    for ($i = 1; $i <= $numberOfDays; $i++):
                                        if ($i <= $today):
                                            $earning = getEarningObjectByDay($i, $pageViewsCounts);
                                            if (!empty($earning)):?>
                                                <tr>
                                                    <td><?= replaceMonthName(date("M j, Y", strtotime($earning->date))); ?></td>
                                                    <td><?= $earning->count; ?></td>
                                                    <td><?= priceFormatted($earning->total_amount, getRewardPriceDecimal()); ?></td>
                                                </tr>
                                            <?php endif;
                                        endif;
                                    endfor;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalNewPayout" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?= trans("new_payout_request"); ?></h4>
            </div>
            <form action="<?= base_url('Earnings/newPayoutRequestPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="modal-body">
                    <div class="col-12">
                        <div class="new-payout-request">
                            <div class="form-group">
                                <label class="control-label"><?= trans("withdraw_amount"); ?></label>
                                <div class="input-group">
                                    <div class="input-group-addon"><b><?= $generalSettings->currency_symbol; ?></b></div>
                                    <input type="number" class="form-control" name="amount" min="0" max="999999" step="0.01" placeholder="E.g. 50" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= trans("withdraw_method"); ?></label>
                                <select name="payout_method" class="form-control" required>
                                    <option value=""><?= trans("select"); ?></option>
                                    <?php if (payoutMethod('paypal_status') && !empty(userPayoutMethod(user(), 'paypal_email'))): ?>
                                        <option value="paypal"><?= trans("paypal"); ?></option>
                                    <?php endif;
                                    if (payoutMethod('bitcoin_status') && !empty(userPayoutMethod(user(), 'btc_address'))): ?>
                                        <option value="bitcoin"><?= trans("bitcoin"); ?></option>
                                    <?php endif;
                                    if (payoutMethod('iban_status') && !empty(userPayoutMethod(user(), 'iban_number'))): ?>
                                        <option value="iban"><?= trans("iban"); ?></option>
                                    <?php endif;
                                    if (payoutMethod('swift_status') && !empty(userPayoutMethod(user(), 'swift_code'))): ?>
                                        <option value="swift"><?= trans("swift"); ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="minimum-payout-container" style="font-size: 15px;">
                            <div class="font-w-700 m-b-15"><?= trans("min_poyout_amounts"); ?></div>
                            <?php if (payoutMethod('paypal_status')): ?>
                                <span><?= trans("paypal"); ?></span>:&nbsp;<strong class="font-w-700"><?= priceFormatted(payoutMethod('paypal_min_amount')); ?></strong>&nbsp;&nbsp;
                            <?php endif;
                            if (payoutMethod('bitcoin_status')): ?>
                                <span><?= trans("bitcoin"); ?></span>:&nbsp;<strong class="font-w-700"><?= priceFormatted(payoutMethod('bitcoin_min_amount')); ?></strong>&nbsp;&nbsp;
                            <?php endif;
                            if (payoutMethod('iban_status')): ?>
                                <span><?= trans("iban"); ?></span>:&nbsp;<strong class="font-w-700"><?= priceFormatted(payoutMethod('iban_min_amount')); ?></strong>&nbsp;&nbsp;
                            <?php endif;
                            if (payoutMethod('swift_status')): ?>
                                <span><?= trans("swift"); ?></span>:&nbsp;<strong class="font-w-700"><?= priceFormatted(payoutMethod('swift_min_amount')); ?></strong>
                            <?php endif; ?>
                            <hr>
                            <strong><?= trans("your_balance"); ?>:&nbsp;<span class="balance text-success" style="font-size: 17px;"><?= priceFormatted(user()->balance); ?></span></strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><?= trans("submit"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .minimum-payout-container {
        padding: 30px;
        background-color: #f8f8f8;
    }

    .minimum-payout-container hr {
        border-top: 1px solid #e0e0e0;
    }

    .text-success {
        color: #309E6B;
    }
</style>


<?php $viewsByDay = [];
foreach ($pageViewsCounts as $item) {
    $day = (int)date('d', strtotime($item->date));
    $viewsByDay[$day] = isset($viewsByDay[$day]) ? $viewsByDay[$day] + $item->count : $item->count;
} ?>


<script src="<?= base_url('assets/admin/plugins/chart/chart.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/plugins/chart/utils.js'); ?>"></script>
<script src="<?= base_url('assets/admin/plugins/chart/analyser.js'); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var numberOfDays = parseInt('<?= $numberOfDays; ?>');
        var presets = window.chartColors;
        var utils = Samples.utils;

        function generateLabels() {
            var labels = [];
            for (var i = 1; i <= numberOfDays; i++) {
                labels.push(i.toString().padStart(2, '0'));
            }
            return labels;
        }

        var currentDate = new Date();
        var year = currentDate.getFullYear();
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');

        var optionsBar = {
            maintainAspectRatio: false,
            spanGaps: false,
            elements: {
                line: {
                    tension: 1
                }
            },
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0
                    }
                },
                xAxes: [
                    {
                        scaleLabel: {
                            display: true,
                            labelString: ''
                        }
                    }
                ],
                yAxes: [
                    {
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) {
                                return parseInt(value);
                            }
                        }
                    }
                ]
            },
            tooltips: {
                enabled: true,
                callbacks: {
                    title: function (tooltipItems) {
                        var day = tooltipItems[0].label.padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    },
                    label: function (tooltipItem) {
                        return `<?= clrQuotes(trans("pageviews")); ?>: ${tooltipItem.value}`;
                    }
                }
            },
            legend: {
                onClick: null
            }
        };

        utils.srand(8);
        new Chart('chart-1', {
            type: 'bar',
            data: {
                labels: generateLabels(),
                datasets: [{
                    backgroundColor: '#49b6ce',
                    borderColor: '#49b6ce',
                    data: [
                        <?php for ($i = 1; $i <= $numberOfDays; $i++) {
                        if ($i <= $today) {
                            if ($i != 1) {
                                echo ',';
                            }
                            echo isset($viewsByDay[$i]) ? $viewsByDay[$i] : '0';
                        }
                    } ?>
                    ],
                    label: '<?= trans("pageviews") ?>: <?= replaceMonthName(date("M Y"));?>',
                    fill: 'start'
                }]
            },
            options: Chart.helpers.merge(optionsBar, {
                title: {
                    text: 'fill: start',
                    display: false
                }
            })
        });


        var optionsLine = {
            maintainAspectRatio: false,
            spanGaps: false,
            elements: {
                line: {
                    tension: 0.000001
                }
            },
            plugins: {
                filler: {
                    propagate: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0
                    }
                },
                xAxes: [
                    {
                        scaleLabel: {
                            display: true,
                            labelString: ''
                        }
                    }
                ],
                yAxes: [
                    {
                        ticks: {
                            beginAtZero: true,
                            callback: function (label) {
                                return "<?= esc($generalSettings->currency_symbol); ?>" + label.toFixed(4);
                            }
                        }
                    }
                ]
            },
            tooltips: {
                enabled: true,
                callbacks: {
                    title: function (tooltipItems) {
                        var day = tooltipItems[0].label.padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    },
                    label: function (tooltipItem) {
                        return `<?= clrQuotes(trans("earnings")); ?>: $${tooltipItem.value}`;
                    }
                }
            },
            legend: {
                onClick: null
            }
        };

        utils.srand(8);
        new Chart('chart-2', {
            type: 'line',
            data: {
                labels: generateLabels(),
                datasets: [{
                    backgroundColor: utils.transparentize(presets.green),
                    borderColor: presets.green,
                    data: [
                        <?php for ($i = 1; $i <= $numberOfDays; $i++) {
                        if ($i <= $today) {
                            if ($i != 1) {
                                echo ',';
                            }
                            $earning = getEarningObjectByDay($i, $pageViewsCounts);
                            echo !empty($earning) && !empty($earning->total_amount)
                                ? number_format($earning->total_amount, getRewardPriceDecimal(), '.', '')
                                : '0';
                        }
                    } ?>
                    ],
                    label: '<?= trans("earnings") ?>: <?= replaceMonthName(date("M Y"));?>',
                    fill: 'start'
                }]
            },
            options: Chart.helpers.merge(optionsLine, {
                title: {
                    text: 'fill: start',
                    display: false
                }
            })
        });
    });
</script>