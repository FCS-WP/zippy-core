import React from "react";
import { Col, Row } from "react-bootstrap";
function MainChartTitle({averageSales, totalSale , ...props}) {
  return (
    <>
      <Row>
        <Col sm="6">
          <h4>Total Sales</h4>
        </Col>
        <Col sm="3">
          <h4>${averageSales}</h4>
          <label>Average sale</label>
        </Col>
        <Col sm="3">
          <h4>${totalSale}</h4>
          <label>Total sale without tax</label>
        </Col>
      </Row>
    </>
  );
}
export default MainChartTitle;
