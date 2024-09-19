import React, { useRef, useState } from "react";
import { Row, Col, Card, CardBody } from "react-bootstrap";
import MainChart from "../Charts/MainChart/MainChart";
import RightChart from "../Charts/RightChart/RightChart";
import ReportFilter from "../Filter/ReportFilter";
import TopTotal from "../Charts/RightChart/TopTotal";
import { DateHelper } from "../../helper/date-helper";
function Content() {
  const [periodFilter, setPeriodFilter] = useState("week");
  const [activeFilter, setActiveFilter] = useState("week");
  const [dateSelected, setDateSelected] = useState();

  const currentDate = new Date();
  const after = new Date(currentDate);
  after.setMonth(currentDate.getMonth() - 7);
  const before = new Date(currentDate);
  before.setHours(23, 59, 59, 999);
  const [orderParams, setOrderParams] = useState({
    interval: "day",
    after: after.toISOString(),
    before: before.toISOString(),
  });
  const [categoriesParams, setCategoriesParams] = useState({
    after: after.toISOString(),
    before: before.toISOString(),
    extended_info: true,
    orderby: "net_revenue",
  });

  // useEffect(() => {
  //   setOrderParams({
  //     interval: "day",
  //     after: after.toISOString(),
  //     before: before.toISOString(),
  //   });
  // }, []);

  const clickFilter = (key) => {
    setPeriodFilter(key);
    setActiveFilter(key);
  };
  const onClickChart = (date) => {
    const { date_start, date_end } = DateHelper.convertDateToRange(date);
    setCategoriesParams({
      after: date_start,
      before: date_end,
      extended_info: true,
      orderby: "net_revenue",
    });
    setOrderParams({
      after: date_start,
      before: date_end,
      interval: "day",
    });
    setDateSelected(date);
    // console.log("Date Start:", date_start);
    // console.log("Date End:", date_end);
  };
  const onClearDate = () => {
    setOrderParams({
      interval: "day",
      after: after.toISOString(),
      before: before.toISOString(),
    });
    setDateSelected(null);
    setCategoriesParams({
      after: after.toISOString(),
      before: before.toISOString(),
      extended_info: true,
      orderby: "net_revenue",
    });

    // console.log('shin');
  };

  return (
    <div id="zippy-content">
      <Row>
        <Col sm="12">
          <ReportFilter
            onClearDate={onClearDate}
            dateSelected={dateSelected}
            activeFilter={activeFilter}
            onClick={clickFilter}
          />
        </Col>
      </Row>
      <Row>
        <Col sm="6">
          <MainChart filterParams={periodFilter} onClickChart={onClickChart} />
        </Col>
        <Col sm="6">
          <Row>
            <TopTotal params={orderParams} />
            <Col>
              <RightChart categoriesParams={categoriesParams} />
            </Col>
          </Row>
        </Col>
      </Row>
    </div>
  );
}
export default Content;
