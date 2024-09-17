import React from "react";
import { Row, Col, Card, CardBody } from "react-bootstrap";
import MainChart from "../Charts/MainChart";
import RightChart from "../Charts/RightChart";

function Content() {
  return (
    <div id="zippy-content">
      <Row>
        <Col sm="12">
          <CardBody>Filter</CardBody>
        </Col>
      </Row>
      <Row>
        <Col sm="8">
          <MainChart />
        </Col>
        <Col sm="4">
          <Row>
            <Col>
              <Card>
                <CardBody>Total Transaction</CardBody>
              </Card>
            </Col>
            <Col>
              <Card>
                <CardBody>Details of Order</CardBody>
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
