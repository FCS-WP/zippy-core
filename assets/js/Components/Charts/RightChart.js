import React, { useEffect } from "react";
import { Chart } from "chart.js";
import { Card, CardBody } from "react-bootstrap";
function RightChart() {
  useEffect(() => {
    const rightChart = document.getElementById("rightChart");
    new Chart(rightChart, {
      type: "bar",
      data: {
        tension: 0.1,
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [
          {
            label: "# of Votes",
            data: [12, 19, 3, 5, 2, 3],
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });
  }, []);

  return (
    <Card>
      <CardBody>
        <canvas id="rightChart" width="700" height="400"></canvas>
      </CardBody>
    </Card>
  );
}
export default RightChart;
