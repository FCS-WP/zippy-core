import React, { useEffect, useCallback, useState } from "react";
import { Row, Col, Card, CardBody } from "react-bootstrap";
import MainChart from "../Charts/MainChart/MainChart";
import RightChart from "../Charts/RightChart/RightChart";
import ReportFilter from "../Filter/ReportFilter";
import TopTotal from "../Charts/RightChart/TopTotal";
import { DateHelper } from "../../helper/date-helper";
import MainChartTitle from "../Charts/MainChart/MainChartTitle";
import { Woocommerce } from "../../Woocommerce/woocommerce";
import ViewType from "../Charts/MainChart/ViewType";
const Content = () => {
  const [periodFilter, setPeriodFilter] = useState("week");

  const [activeFilter, setActiveFilter] = useState("last_week");
  const [dateSelected, setDateSelected] = useState();
  const [viewTypeSelected, setViewTypeSelected] = useState("day");
  const [currentView, setcurrentView] = useState("");
  const [netSales, setNetSales] = useState(0);
  const [totalSale, setTotalSale] = useState(0);

  const last_week = DateHelper.getDate();

  const [dateParams, setDateParams] = useState({
    after: last_week.date_start,
    before: last_week.date_end,
  });

  const [orderParams, setOrderParams] = useState({
    interval: "day",
    after: dateParams.after,
    before: dateParams.before,
  });

  const [categoriesParams, setCategoriesParams] = useState({
    after: dateParams.after,
    before: dateParams.before,
    extended_info: true,
    orderby: "net_revenue",
  });

  const [mainChartParams, setMainChartParams] = useState({
    interval: "day",
    after: dateParams.after,
    before: dateParams.before,
    fields: ["net_revenue", "items_sold"],
    order: "asc",
    per_page: 100,
  });

  const clickFilter = (key) => {
    setcurrentView("");

    setPeriodFilter(key);
    const date = DateHelper.getDate(key);
    setDateParams(date);
    setOrderParams({
      interval: "day",
      after: date.date_start,
      before: date.bedate_endfore,
    });
    setActiveFilter(key);

    setMainChartParams((prev) => ({
      ...prev,
      after: date.date_start,
      before: date.date_end,
    }));
    setCategoriesParams((prev) => ({
      ...prev,
      after: date.date_start,
      before: date.date_end,
    }));
    setDateSelected({
      name: "",
      type: "",
    });
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

  const onClearDate = (type) => {
    if (type === "day") {
      setOrderParams({
        interval: "day",
        after: dateParams.date_start,
        before: dateParams.bedate_endfore,
      });
      setCategoriesParams({
        after: dateParams.date_start,
        before: dateParams.bedate_endfore,
        extended_info: true,
        orderby: "net_revenue",
      });
    } else {
      setMainChartParams({
        interval: viewTypeSelected,
        after: dateParams.date_start,
        before: dateParams.bedate_endfore,
        fields: ["net_revenue", "items_sold"],
        order: "asc",
        per_page: 100,
      });
    }
    setOrderParams({
      interval: "day",
      after: dateParams.date_start,
      before: dateParams.bedate_endfore,
    });
    setDateSelected({
      name: "",
      type: "",
    });
    setcurrentView("");
  };

  const onClickViewType = (type) => {
    setOrderParams({
      interval: "day",
      after: dateParams.date_start,
      before: dateParams.bedate_endfore,
    });
    setCategoriesParams({
      after: dateParams.date_start,
      before: dateParams.bedate_endfore,
      extended_info: true,
      orderby: "net_revenue",
    });
    setMainChartParams((prev) => ({ ...prev, interval: type }));
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

    setOrderParams((prev) => ({ ...prev, categories: cate_id }));
    setMainChartParams((prev) => ({ ...prev, categories: cate_id }));
    setDateSelected({
      name: name,
      type: "category",
    });
    setcurrentView("category");
  };

  const handleCustomDate = (date) => {
    setDateParams((prev) => ({
      ...prev,
      date_start: DateHelper.getDateToString(date.date_start, "23:59:00"),
      date_end: DateHelper.getDateToString(date.date_end, "00:00:00"),
    }));
  };

  useEffect(() => {
    setMainChartParams((prev) => ({
      ...prev,
      after: dateParams.date_start,
      before: dateParams.date_end,
    }));
    setCategoriesParams((prev) => ({
      ...prev,
      after: dateParams.date_start,
      before: dateParams.date_end,
    }));
  }, [dateParams]);
  const fetchData = useCallback(async (params) => {
    const { data } = await Woocommerce.getOrderData(params);
    const dataTotal = data.totals;
    setNetSales(dataTotal.net_revenue || 0);
    setTotalSale(dataTotal.net_revenue || 0);
    // setProductSold(dataTotal.items_sold || 0);
  }, []);

  useEffect(() => {
    fetchData(categoriesParams);
  }, [categoriesParams]);
  return (
    <div id="zippy-content">
      <Row>
        <Col sm="12">
          <ReportFilter
            activeFilter={activeFilter}
            onClick={clickFilter}
            handleCustomDate={handleCustomDate}
          />
        </Col>
      </Row>
      <Row>
        <Col sm="6">
          <Card className="mt-0">
            <MainChartTitle netSales={netSales} totalSale={totalSale} />
            <ViewType
              onClearDate={onClearDate}
              viewTypeSelected={viewTypeSelected}
              onClickViewType={onClickViewType}
              dateSelected={dateSelected}
            />
            <MainChart
              onClearDate={onClearDate}
              dateSelected={dateSelected}
              mainChartParams={mainChartParams}
              onClickChart={onClickChart}
            />
          </Card>
        </Col>
        <Col sm="6">
          <Row className="right-chart">
            <TopTotal params={orderParams} />
            <Col sm="12">
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
};
export default Content;
