import React, { useRef, useState } from "react";
import { Row, Col, Card, CardBody } from "react-bootstrap";
import MainChart from "../Charts/MainChart/MainChart";
import RightChart from "../Charts/RightChart/RightChart";
import ReportFilter from "../Filter/ReportFilter";
import TopTotal from "../Charts/RightChart/TopTotal";
import { DateHelper } from "../../helper/date-helper";
function Content() {
  const [activeFilter, setActiveFilter] = useState("last_week");
  const [dateSelected, setDateSelected] = useState();
  const [viewTypeSelected, setViewTypeSelected] = useState("day");
  const [currentView, setcurrentView] = useState("");

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

  const [mainChartParams, setMainChartParams] = useState({
    interval: "day",
    after: after.toISOString(),
    before: before.toISOString(),
    fields: ["net_revenue", "items_sold"],
    order: "asc",
    per_page: 100,
  });
  const clickFilter = (key) => {
    setPeriodFilter(key);
    setActiveFilter(key);
  };

  const onClickChart = (date) => {
    const { date_start, date_end } = date;

    if (currentView == "category") return;
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
    const dataSelected = DateHelper.getDateOutputSelect(date, viewTypeSelected);
    setDateSelected({
      name: dataSelected,
      type: "day",
    });
    setcurrentView("day");
  };

  const onClearDate = () => {
    setOrderParams({
      interval: "day",
      after: after.toISOString(),
      before: before.toISOString(),
    });
    setDateSelected({
      name: "",
      type: "",
    });
    setcurrentView("");
    setCategoriesParams({
      after: after.toISOString(),
      before: before.toISOString(),
      extended_info: true,
      orderby: "net_revenue",
    });
    setMainChartParams({
      interval: "day",
      after: after.toISOString(),
      before: before.toISOString(),
      fields: ["net_revenue", "items_sold"],
      order: "asc",
      per_page: 100,
    })
  };

  const onClickViewType = (type) => {
    setMainChartParams({
      interval: type,
      after: after.toISOString(),
      before: before.toISOString(),
      fields: ["net_revenue", "items_sold"],
      order: "asc",
      per_page: 100,
    });
    
    setViewTypeSelected(type);
    setcurrentView("");
    setDateSelected({
      name: "",
      type: "",
    });
  };
  const onClickBarCallback = (cate_id, name) => {
    if (currentView == "day") {
      return;
    }

    setOrderParams({
      interval: "day",
      after: after.toISOString(),
      before: before.toISOString(),
      categories: cate_id,

    });
    setMainChartParams({
      interval: "day",
      after: after.toISOString(),
      before: before.toISOString(),
      fields: ["net_revenue", "items_sold"],
      order: "asc",
      categories: cate_id,
      per_page: 100,
    });
    setDateSelected({
      name: name,
      type: "category",
    });
    setcurrentView("category");
  };
  return (
    <div id="zippy-content">
      <Row>
        <Col sm="12">
          <ReportFilter
            viewTypeSelected={viewTypeSelected}
            onClickViewType={onClickViewType}
            onClearDate={onClearDate}
            dateSelected={dateSelected}
            activeFilter={activeFilter}
            onClick={clickFilter}
          />
        </Col>
      </Row>
      <Row>
        <Col sm="6">
          <MainChart
            mainChartParams={mainChartParams}
            onClickChart={onClickChart}
          />
        </Col>
        <Col sm="6">
          <Row>
            <TopTotal params={orderParams} />
            <Col>
              <RightChart
                onClickCallback={onClickBarCallback}
                categoriesParams={categoriesParams}
              />
            </Col>
          </Row>
        </Col>
      </Row>
    </div>
  );
}
export default Content;
