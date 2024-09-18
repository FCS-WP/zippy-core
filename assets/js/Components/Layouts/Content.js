import React, { useState } from "react";
import { Row, Col, Card, CardBody } from "react-bootstrap";
import MainChart from "../Charts/MainChart/MainChart";
import RightChart from "../Charts/RightChart/RightChart";

import ReportFilter from "../Filter/ReportFilter";
function Content() {
  const [periodFilter, setPeriodFilter] = useState("week");
  const [activeFilter, setActiveFilter] = useState("week");
  const clickFilter = (key) => {
    setPeriodFilter(key);
    setActiveFilter(key);
  };

  return (
    <div id="zippy-content">
      <Row>
        <Col sm="12">
          <ReportFilter activeFilter={activeFilter} onClick={clickFilter} />
        </Col>
      </Row>
      <Row>
        <Col sm="6">
          <MainChart filterParams={periodFilter} />
        </Col>
        <Col sm="6">
          <Row>
            <Col>
              <Card>
                <CardBody>Orders</CardBody>
              </Card>
            </Col>
            <Col>
              <Card>
                <CardBody>Products Sold</CardBody>
              </Card>
            </Col>
          </Row>
          <Row>
            <Col>
              <RightChart />
            </Col>
          </Row>
        </Col>
      </Row>
    </div>
  );
}
export default Content;
