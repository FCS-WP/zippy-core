import React, { useEffect, useMemo, useState } from "react";
// import { Chart } from "chart.js";
import { Card, CardBody } from "react-bootstrap";
import { Bar } from "react-chartjs-2";
import { Woocommerce } from "../../../Woocommerce/woocommerce";
const RightChart = () => {
  // Char Setting
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
          display: false,
        },
      },
    }),
    []
  );
  const [chartData, setChartData] = useState({
    labels: [],
    datasets: [
      {
        label: "Net Revenue",
        data: [],
        tension: 0.1,
      },
    ],
  });
  const currentDate = new Date();
  const after = new Date(currentDate);
  after.setMonth(currentDate.getMonth() - 7);
  const before = new Date(currentDate);
  before.setHours(23, 59, 59, 999);
  const [params, setParams] = useState({
    after: after.toISOString(),
    before: before.toISOString(),
    extended_info: true,
    orderby: "net_revenue",
  });
  const fechData = async (params) => {
    const { data } = await Woocommerce.getCategoriesSale(params);
    // const dataTotal = data.totals;
    // const dataCategoriesName = Object.keys(data.extended_info) || 0;
    const dataCategoriesName = Object.keys(data).map(
      (key) => data[key].extended_info.name
    );
    const dataNetRevenue = Object.keys(data).map(
      (key) => data[key].net_revenue
    );
    // console.log(dataCategoriesName);
    setChartData({
      labels: dataCategoriesName,
      datasets: [
        {
          label: "Net Revenue",
          data: dataNetRevenue,
          tension: 0.1,
        },
      ],
    });
  };
  useEffect(() => {
    fechData(params);
  }, [params]);
  return (
    <Card>
      <CardBody>
        <Bar data={chartData} options={options}></Bar>
        <h5 className="mt-2 text-center">Top 10 Categories</h5>
      </CardBody>
    </Card>
  );
};
export default RightChart;
