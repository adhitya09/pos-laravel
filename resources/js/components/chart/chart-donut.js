import ApexCharts from 'apexcharts';

const currencyFormatter = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

export function initDashboardDonutCharts() {
    const chartPaymentMethodsEl = document.querySelector('#chartPaymentMethods');
    const chartCashFlowEl = document.querySelector('#chartCashFlow');
    const chartData = window.dashboardChartData || {};

    if (chartPaymentMethodsEl && Array.isArray(chartData.paymentMethods) && chartData.paymentMethods.length) {
        const paymentMethodOptions = {
            series: chartData.paymentMethods,
            labels: chartData.paymentMethodLabels || [],
            colors: ['#465FFF', '#10B981', '#F97316', '#A855F7', '#F43F5E'],
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif',
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontFamily: 'Inter, sans-serif',
                markers: {
                    radius: 999,
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '14px',
                                color: '#94A3B8',
                            },
                            value: {
                                show: true,
                                fontSize: '24px',
                                color: '#0F172A',
                                formatter: function (val) {
                                    return val;
                                },
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#64748B',
                                formatter: function () {
                                    return chartData.paymentMethods.reduce((sum, value) => sum + value, 0);
                                },
                            },
                        },
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return `Jumlah: ${val}`;
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 640,
                    options: {
                        chart: {
                            height: 280,
                        },
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            ],
        };

        const paymentMethodChart = new ApexCharts(chartPaymentMethodsEl, paymentMethodOptions);
        paymentMethodChart.render();
    }

    if (chartCashFlowEl && Array.isArray(chartData.cashFlowSeries) && chartData.cashFlowSeries.length) {
        const cashFlowOptions = {
            series: chartData.cashFlowSeries,
            labels: chartData.cashFlowLabels || ['Cash Inflow', 'Cash Outflow'],
            colors: ['#10B981', '#EF4444'],
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif',
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontFamily: 'Inter, sans-serif',
                markers: {
                    radius: 999,
                },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '14px',
                                color: '#94A3B8',
                            },
                            value: {
                                show: true,
                                fontSize: '24px',
                                color: '#0F172A',
                                formatter: function (val) {
                                    return currencyFormatter(val);
                                },
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                color: '#64748B',
                                formatter: function () {
                                    return currencyFormatter(chartData.cashFlowSeries.reduce((sum, value) => sum + value, 0));
                                },
                            },
                        },
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return currencyFormatter(val);
                    },
                },
            },
            responsive: [
                {
                    breakpoint: 640,
                    options: {
                        chart: {
                            height: 280,
                        },
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            ],
        };

        const cashFlowChart = new ApexCharts(chartCashFlowEl, cashFlowOptions);
        cashFlowChart.render();
    }
}

export default initDashboardDonutCharts;
