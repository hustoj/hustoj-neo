<template>
    <div class="main">
        <p>Dashboard <el-select v-model="from" @change="changeChartRange">
            <el-option v-for="item in options" :key="item.value"
                :label="item.label" :value="item.value">
            </el-option>
        </el-select>
        </p>
        <el-row :gutter="20">
            <el-col :span="12">
                <div class="grid-content">
                    <div id="register-chart"></div>
                </div>
            </el-col>
            <el-col :span="12">
                <div class="grid-content">
                    <div id="submission-chart"></div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>

<style lang="scss" scoped>
    #register-chart, #submission-chart {
        height: 400px;
    }
</style>

<script>
    import echarts from 'echarts';

    export default {
        data() {
            return {
                from: 7,
                options: [ {
                    label: 'last 7 days',
                    value: 7
                }, {
                    label: 'last 10 days',
                    value: 10
                }, {
                    label: 'last 30 days',
                    value: 30
                }],
                allCharts: [
                    {
                        id: 'register-chart',
                        key: 'user',
                        title: 'Register User',
                        seriesName: 'Number',
                    }, {
                        id: 'submission-chart',
                        key: 'submission',
                        seriesName: 'Number',
                        title: 'Submission',
                    }
                ],
                charts: {},
            };
        },
        mounted() {
            let self = this;
            function addBarChart(id, config) {
                let el_chart = document.getElementById(id);
                self.charts[id] = echarts.init(el_chart);
                self.charts[id].setOption({
                    title: { text: config.title },
                    tooltip: {},
                    xAxis: {
                        data: config.xTitle
                    },
                    yAxis: {},
                    series: [{
                        name: config.seriesName,
                        type: 'bar',
                        data: config.data
                    }]
                });
            }
            self.allCharts.forEach(function (chart) {
                let config = {
                    title: chart.title,
                    seriesName: chart.seriesName,
                    data: [],
                    xTitle: [],
                };
                addBarChart(chart.id, config);
            });
            self.reloadChart(this.from);
        },
        methods: {
            changeChartRange(from) {
                this.reloadChart(from);
            },
            reloadChart(from) {
                let self = this;
                function setBarChar(id, titles, data) {
                    let el_chart = self.charts[id];
                    el_chart.setOption({
                        xAxis: {data: titles},
                        series: {data: data},
                    });
                }
                this.$http.get('/admin/home/chart?from=' + from)
                    .then(function (res) {
                        self.allCharts.forEach(function (chart) {
                            let titles = [], data = [];
                            res.data[chart.key].forEach(function (item) {
                                titles.push(item.date);
                                data.push(item.number);
                            });
                            setBarChar(chart.id, titles, data);
                        })
                    });
                },
        }
    }
</script>
