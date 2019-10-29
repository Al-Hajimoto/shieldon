<?php defined('SHIELDON_VIEW') || exit('Life is short, why are you wasting time?');
/*
 * This file is part of the Shieldon package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use function Shieldon\Helper\_e;
use function Shieldon\Helper\mask_string;

?>

<div class="so-dashboard">
    <?php if (! empty($period_data)) : ?>
    <div class="so-flex">
        <div class="so-board">
            <div class="board-field left">
                <div id="chart-1"></div>
            </div>
            <div class="board-field right">
                <div class="heading"><?php _e('panel', 'log_heading_captchas', 'CAPTCHAs'); ?></div>
                <div class="nums"><?php echo number_format($period_data['captcha_count']); ?></div>
                <div class="note"><?php _e('panel', 'log_note_captcha_this_month', 'CAPTCHA statistic this month.'); ?></div>
            </div>
        </div>
        <div class="so-board">
            <div class="board-field left">
                <div id="chart-2"></div>
            </div>
            <div class="board-field right">
                <div class="heading"><?php _e('panel', 'log_heading_pageviews', 'Pageviews'); ?></div>
                <div class="nums"><?php echo number_format($period_data['pageview_count']); ?></div>
                <div class="note"><?php _e('panel', 'log_note_pageview_this_month', 'Total pageviews this month.'); ?></div>
            </div>
        </div>
        <div class="so-board area-chart-container">
            <div id="chart-3"></div>
        </div>
    </div>
    <?php endif; ?>
    <div class="so-tabs">
        <ul>
            <li><a href="<?php echo $page_url; ?>&tab=today"><?php _e('panel', 'log_label_today', 'Today'); ?></a></li>
            <li><a href="<?php echo $page_url; ?>&tab=yesterday"><?php _e('panel', 'log_label_yesterday', 'Yesterday'); ?></a></li>
            <li><a href="<?php echo $page_url; ?>&tab=past_seven_days"><?php _e('panel', 'log_label_last_7_days', 'Last 7 days'); ?></a></li>
            <li class="is-active"><a href="<?php echo $page_url; ?>&tab=this_month"><?php _e('panel', 'log_label_this_month', 'This month'); ?></a></li>
            <li><a href="<?php echo $page_url; ?>&tab=last_month"><?php _e('panel', 'log_label_last_month', 'Last month'); ?></a></li>
        </ul>
    </div>

    <?php if ($page_availability) : ?>
        <div id="so-table-loading" class="so-datatables">
            <div class="lds-css ng-scope">
                <div class="lds-ripple">
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-danger">
            <?php _e('panel', 'log_msg_no_logger', 'Sorry, you have to implement ActionLogger to use this function.'); ?>
            
        </div>
    <?php endif; ?>

    <div id="so-table-container" class="so-datatables" style="display: none;">
        <table id="so-datalog" class="cell-border compact stripe responsive" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th rowspan="2"><?php _e('panel', 'ipma_label_ip', 'IP'); ?></th>
                    <th rowspan="2"><?php _e('panel', 'log_label_session', 'Sessions'); ?></th>
                    <th rowspan="2"><?php _e('panel', 'log_label_pageviews', 'Pageviews'); ?></th>
                    <th colspan="3" class="merged-field"><?php _e('panel', 'log_label_captcha', 'CAPTCHA'); ?></th>
                    <th rowspan="2"><?php _e('panel', 'log_label_in_blacklist', 'In blacklist'); ?></th>
                    <th rowspan="2"><?php _e('panel', 'log_label_in_queue', 'In queue'); ?></th>
                </tr>
                <tr>
                    <th><?php _e('panel', 'log_label_solved', 'solved'); ?></th>
                    <th><?php _e('panel', 'log_label_failed', 'failed'); ?></th>
                    <th><?php _e('panel', 'log_label_displays', 'displays'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (! empty($ip_details)) : ?>
                <?php foreach($ip_details as $ip => $ipInfo) : ?>
                <tr>
                    <td>
                        <?php if ($this->mode === 'demo') : ?>
                            <?php $ip = mask_string($ip); ?>
                        <?php endif; ?>
                        <?php echo $ip; ?>
                    </td>
                    <td><?php echo count($ipInfo['session_id']); ?></td>
                    <td><?php echo $ipInfo['pageview_count']; ?></td>
                    <td><?php echo $ipInfo['captcha_success_count']; ?></td>
                    <td><?php echo $ipInfo['captcha_failure_count']; ?></td>
                    <td><?php echo $ipInfo['captcha_count']; ?></td>
                    <td><?php echo $ipInfo['blacklist_count']; ?></td>
                    <td><?php echo $ipInfo['session_limit_count']; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>   
        </table>
    </div>
    <div class="so-timezone">
        <?php _e('panel', 'log_label_timezone', 'Timezone'); ?>: UTC 
    </div>
</div>

<?php if (! empty($period_data)) : ?>

<script>

    // Today
    var todayPieOptions = {
        legend: {
            show: false
        },
        chart: {
            type: 'donut',
        },
        series: [<?php echo $period_data['captcha_success_count']; ?>, <?php echo $period_data['captcha_failure_count']; ?>],
        labels: ['success', 'failure'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    }

    var todayCaptchaPie = new ApexCharts(
        document.querySelector("#chart-1"),
        todayPieOptions
    );
    
    todayCaptchaPie.render();


    // Yesterday
    var yesterdayPieOptions = {
        legend: {
            show: false
        },
        chart: {
            type: 'donut',
        },
        series: [<?php echo $period_data['pageview_count']; ?>, <?php echo $period_data['captcha_count']; ?>],
        labels: ['Pageviews', 'CAPTCHAs'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    }

    var yesterdayCaptchaPie = new ApexCharts(
        document.querySelector("#chart-2"),
        yesterdayPieOptions
    );
    
    yesterdayCaptchaPie.render();

    // This month
    var spark3 = {
        chart: {
            type: 'area',
            sparkline: {
                enabled: true
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        fill: {
            opacity: 1,
        },
        series: [{
            name: 'pageview',
            data: [<?php echo $period_data['pageview_chart_string']; ?>]
        }, {
            name: 'captcha',
            data: [<?php echo $period_data['captcha_chart_string']; ?>]
        }],
        labels: [<?php echo $period_data['label_chart_string']; ?>],
        markers: {
            size: 5
        },
        xaxis: {
            type: 'category',
        },
        yaxis: {
            min: 0
        },
        tooltip: {
            fixed: {
                enabled: false
            },
            x: {
                show: false
            },
            y: {
                title: {
                    formatter: function (seriesName) {
                        return seriesName;
                    }
                }
            },
            marker: {
                show: false
            }
        },
        title: {
            text: '',
            offsetX: 55,
            offsetY: 16,
            style: {
                fontSize: '16px',
                cssClass: 'apexcharts-yaxis-title',
            }
        },
        subtitle: {
            text: '',
            offsetX: 55,
            offsetY: 36,
            style: {
                fontSize: '13px',
                cssClass: 'apexcharts-yaxis-title'
            }
        }
    }

    var chart = new ApexCharts(
        document.querySelector("#chart-3"),
        spark3
    );

    chart.render();

    $(function() {
        $('#so-datalog').DataTable({
            'responsive': true,
            'pageLength': 25,
            'initComplete': function(settings, json ) {
                $('#so-table-loading').hide();
                $('#so-table-container').fadeOut(800);
                $('#so-table-container').fadeIn(800);
            }
        });
    });
    
</script>

<?php endif; ?>