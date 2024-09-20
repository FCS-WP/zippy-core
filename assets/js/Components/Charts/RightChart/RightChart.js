import React, {
  useEffect,
  useMemo,
  useState,
  useCallback,
  useRef,
} from "react";
import { Card, CardBody, Spinner, Alert } from "react-bootstrap";
import { Bar } from "react-chartjs-2";
import { Woocommerce } from "../../../Woocommerce/woocommerce";
import { Line, getElementAtEvent } from "react-chartjs-2";

const RightChart = ({ categoriesParams, onClickCallback }) => {
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

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [data, setdata] = useState(null);
  const chartRef = useRef(null);

  const fetchData = useCallback(async (params) => {
    setLoading(true);
    setError(null);
    try {
      const { data } = await Woocommerce.getCategoriesSale(params);
      // console.log(data);
      setdata(data);
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

  const printElementAtEvent = async (element) => {
    if (!element.length) return;
    const { index } = element[0];

    onClickCallback(data[index].category_id, chartData.labels[index]);
  };

  const handleOnClickChart = (event) => {
    const { current: chart } = chartRef;
    if (!chart) return;
    printElementAtEvent(getElementAtEvent(chart, event));
  };

  return (
    <Card className="mt-0">
      <CardBody>
        {loading && <Spinner animation="border" variant="primary" />}
        {error && <Alert variant="danger">{error}</Alert>}
        <Bar
          ref={chartRef}
          data={chartData}
          options={options}
          onClick={handleOnClickChart}
        />
        <h5 className="mt-2 text-center">Top 10 Categories</h5>
      </CardBody>
    </Card>
  );
};

export default RightChart;
