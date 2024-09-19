import React, { useEffect, useMemo, useState, useCallback } from "react";
import { Card, CardBody, Spinner, Alert } from "react-bootstrap";
import { Bar } from "react-chartjs-2";
import { Woocommerce } from "../../../Woocommerce/woocommerce";

const RightChart = ({ categoriesParams }) => {
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
    },
  }), []);

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

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchData = useCallback(async (params) => {
    setLoading(true);
    setError(null);
    try {
      const { data } = await Woocommerce.getCategoriesSale(params);
      const dataCategoriesName = Object.keys(data).map(
        (key) => data[key].extended_info.name
      );
      const dataNetRevenue = Object.keys(data).map(
        (key) => data[key].net_revenue
      );

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
    } catch (err) {
      setError("Failed to fetch data");
      console.error(err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (categoriesParams) {
      fetchData(categoriesParams);
    }
  }, [categoriesParams, fetchData]);

  return (
    <Card className="mt-0">
      <CardBody>
        {loading && <Spinner animation="border" variant="primary" />}
        {error && <Alert variant="danger">{error}</Alert>}
        <Bar data={chartData} options={options} />
        <h5 className="mt-2 text-center">Top 10 Categories</h5>
      </CardBody>
    </Card>
  );
};

export default RightChart;
