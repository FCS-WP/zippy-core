import React, { useEffect, useMemo, useState, useRef, useCallback } from "react";
import { Card, CardBody, Spinner, Alert } from "react-bootstrap";
import MainChartTitle from "./MainChartTitle";
import { Chart, registerables } from "chart.js";
import { Woocommerce } from "../../../Woocommerce/woocommerce";
import { Line, getElementAtEvent } from "react-chartjs-2";

Chart.register(...registerables);

const MainChart = ({ filterParams, ...props }) => {
  const options = useMemo(() => ({
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
      },
    },
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        callbacks: {
          label: (tooltipItem) => {
            return `Sales: $${tooltipItem.raw.toFixed(2)}`;
          },
        },
      },
    },
  }), []);

  const [netSales, setNetSales] = useState(0);
  const [totalSale, setTotalSale] = useState(0);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  
  const [chartData, setChartData] = useState({
    labels: [],
    datasets: [
      {
        label: "Monthly Revenue",
        data: [],
        fill: false,
        borderColor: "rgba(34, 113, 177, 1)",
        tension: 0.1,
      },
    ],
  });

  const [params, setParams] = useState({
    date_min: "2024-01-01",
    date_max: "2024-12-01",
    period: "week",
  });

  const fetchData = useCallback(async (params) => {
    setLoading(true);
    setError(null);
    try {
      const { data } = await Woocommerce.getTotalSales(params);
      const dataTotal = data[0].totals;
      const dataDate = Object.keys(dataTotal) || [];
      const salesData = dataDate.map(key => parseFloat(dataTotal[key].sales));

      setNetSales(data[0].net_sales || 0);
      setTotalSale(data[0].total_sales || 0);
      setChartData({
        labels: dataDate,
        datasets: [{
          label: "Monthly Revenue",
          data: salesData,
          borderWidth: 2,
          backgroundColor: "rgba(34, 113, 177, 1)",
          borderColor: "rgba(34, 113, 177, 1)",
        }],
      });
    } catch (err) {
      setError("Failed to fetch data");
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    handleFilterChange(filterParams);
  }, [filterParams]);

  useEffect(() => {
    fetchData(params);
  }, [params, fetchData]);

  const chartRef = useRef(null);

  const handleFilterChange = (filterParams) => {
    setParams({
      date_min: "2024-01-01",
      date_max: "2024-12-01",
      period: filterParams,
    });
  };

  const printElementAtEvent = (element) => {
    if (!element.length) return;
    const { datasetIndex, index } = element[0];
    console.log(`Sales on ${chartData.labels[index]}: $${chartData.datasets[datasetIndex].data[index].toFixed(2)}`);
  };

  const onClickChart = (event) => {
    const { current: chart } = chartRef;
    if (!chart) return;
    printElementAtEvent(getElementAtEvent(chart, event));
  };

  return (
    <Card className="mt-0">
      <CardBody className="border-bottom">
        <MainChartTitle netSales={netSales} totalSale={totalSale} />
      </CardBody>
      <CardBody className="">
        {loading && <Spinner animation="border" variant="primary" />}
        {error && <Alert variant="danger">{error}</Alert>}
        <Line
          ref={chartRef}
          data={chartData}
          onClick={onClickChart}
          options={options}
        />
      </CardBody>
    </Card>
  );
};

export default MainChart;
