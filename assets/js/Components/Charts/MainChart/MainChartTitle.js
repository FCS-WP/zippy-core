import React from "react";
import { Col, Row } from "react-bootstrap";
const MainChartTitle = ({ netSales, totalSale, ...props }) => {
  return (
    <Row>
      <Col sm="6">
        <h4>Total Sales</h4>
      </Col>
      <Col sm="3">
        <label>Total Sales</label>

        <h5>${totalSale}</h5>
      </Col>
      <Col sm="3">
        <label>Net Sales</label>

        <h5>${netSales}</h5>
      </Col>
    </Row>
  );
};
export default MainChartTitle;
