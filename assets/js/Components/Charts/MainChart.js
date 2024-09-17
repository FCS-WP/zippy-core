import React, { useEffect, useMemo, useState } from "react";

import { Card, CardBody } from "react-bootstrap";
import MainChartTitle from "../Title/MainChartTitle";
import { Chart, registerables } from "chart.js";
import { Woocommerce } from "../../Woocommerce/woocommerce";
import { Line } from "react-chartjs-2";
Chart.register(...registerables);
const MainChart = () => {
  const params = useMemo(
    () => ({
      date_min: "2024-01-03",
      date_max: "2024-12-01",
    }),
    []
  );

  const options = useMemo(
    () => ({
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      plugins: {
        legend: {
          display: false, // Hide the legend
        },
      },
    }),
    []
  );

  const [averageSales, setaverageSales] = useState();

  const [totalSale, settotalSale] = useState();

  // const [chartDate, setchartDate] = useState();
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

  const fechData = async () => {
    const { data } = await Woocommerce.getTotalSales(params);
    const dataTotal = data.totals;
    const dataDate = Object.keys(dataTotal) || 0;
    const salesData = Object.keys(dataTotal).map(
      (key) => parseFloat(dataTotal[key].sales)

      // parseFloat(data[key].sales)
    );

    setaverageSales(data.average_sales || 0);
    settotalSale(data.total_sales || 0);
    // setchartDate(dataDate);
    setChartData({
      tension: 0.1,
      labels: dataDate,
      datasets: [
        {
          label: [],
          data: salesData,
          borderWidth: 2,
          backgroundColor: "rgba(34, 113, 177, 1)",
          borderColor: "rgba(34, 113, 177, 1)",
        },
      ],
    });
    console.log(data);
    console.log(salesData);
  };

  // useEffect(() => {
  //   Chart.register(...registerables);
  //   const mainChart = document.getElementById("mainChart");

  //   new Chart(mainChart, {
  //     type: "line",
  //     data: {
  //       tension: 0.1,
  //       labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
  //       datasets: [
  //         {
  //           label: "# of Votes",
  //           data: [12, 19, 3, 5, 2, 3],
  //           borderWidth: 1,
  //         },
  //       ],
  //     },
  //     options: {
  //       responsive: true,
  //       scales: {
  //         y: {
  //           beginAtZero: true,
  //         },
  //       },
  //     },
  //   });
  // }, []);

  useEffect(() => {
    fechData();
  }, [params]);

  const updateChart = () => {
    let chart_main = Chart.getChart(mainChart);
    console.log(chart_main);
  };

  return (
    <Card>
      <CardBody>
        <MainChartTitle averageSales={averageSales} totalSale={totalSale} />
      </CardBody>
      <CardBody>
        <Line data={chartData} options={options} />
        {/* <canvas id="mainChart" width="700" height="400"></canvas> */}
      </CardBody>
    </Card>
  );
};
export default MainChart;
